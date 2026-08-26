<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\StokGudang;
use App\Models\BarangKeluar;
use App\Models\DetailBarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeGudang = auth()->user()->getActiveGudangCode();
        $query = BarangKeluar::with(['gudang', 'user']);

        if ($activeGudang !== 'all') {

            $query->where('gudang_asal_kode', $activeGudang);

        }

        $transactions = $query->orderBy('tanggal_keluar', 'asc')->get();
        return view('barang_keluar.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $activeGudang = $user->getActiveGudangCode();
        $barangs = Barang::with('satuan')->orderBy('nama_barang', 'asc')->get();
        
        $gudangs = Gudang::where('kode_gudang', $activeGudang)->get();
        $allGudangs = Gudang::orderBy('nama_gudang', 'asc')->get();

        $satuans = \App\Models\Satuan::orderBy('nama_satuan', 'asc')->get();
        return view('barang_keluar.create', compact('barangs', 'gudangs', 'allGudangs', 'satuans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_surat_jalan' => 'required|string|max:100|unique:barang_keluars,no_surat_jalan',
            'tanggal_keluar' => 'required|date',
            'tanggal_surat_jalan' => 'required|date',
            'jenis' => 'required|in:reguler,mutasi',
            'gudang_asal_kode' => 'required|exists:gudangs,kode_gudang',
            'gudang_tujuan_kode' => 'required_if:jenis,mutasi',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.satuan_id' => 'nullable|exists:satuans,id',
            'items.*.qty' => 'required|numeric|min:0.01',
        ]);

        // CRITICAL VALIDATION: Check warehouse stock for each item before saving
        foreach ($request->items as $item) {
            $stok = StokGudang::where('kode_gudang', $request->gudang_asal_kode)
                              ->where('barang_id', $item['barang_id'])
                              ->first();
            
            $currentStock = $stok ? $stok->stok_sekarang : 0;
            if ($item['qty'] > $currentStock) {
                $barangObj = Barang::find($item['barang_id']);
                $gudangObj = Gudang::where('kode_gudang', $request->gudang_asal_kode)->first();
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['stock' => "Stok untuk barang \"{$barangObj->nama_barang}\" di \"{$gudangObj->nama_gudang}\" tidak mencukupi! Stok saat ini: " . number_format($currentStock, 0, ',', '.') . ", permintaan keluar: " . number_format($item['qty'], 0, ',', '.')]);
            }
        }

        DB::transaction(function() use ($request) {
            $status = $request->jenis === 'mutasi' ? 'pending' : 'completed';

            $barangKeluar = BarangKeluar::create([
                'no_surat_jalan' => $request->no_surat_jalan,
                'tanggal_keluar' => $request->tanggal_keluar,
                'tanggal_surat_jalan' => $request->tanggal_surat_jalan,
                'jenis' => $request->jenis,
                'gudang_asal_kode' => $request->gudang_asal_kode,
                'gudang_tujuan_kode' => $request->jenis === 'mutasi' ? $request->gudang_tujuan_kode : null,
                'status' => $status,
                'user_id' => Auth::id() ?: 1,
            ]);

            // 2. Create detail and decrement stock
            foreach ($request->items as $item) {
                // Auto-update item unit if it is missing
                if (!empty($item['satuan_id'])) {
                    $barangObj = Barang::find($item['barang_id']);
                    if ($barangObj && is_null($barangObj->satuan_id)) {
                        $barangObj->update(['satuan_id' => $item['satuan_id']]);
                    }
                }

                DetailBarangKeluar::create([
                    'barang_keluar_id' => $barangKeluar->id,
                    'barang_id' => $item['barang_id'],
                    'qty' => $item['qty']
                ]);

                // Decrement warehouse stock
                $stok = StokGudang::where('kode_gudang', $request->gudang_asal_kode)
                                  ->where('barang_id', $item['barang_id'])
                                  ->first();
                
                $saldoAwal = $stok ? $stok->stok_sekarang : 0;
                $saldoAkhir = max(0, $saldoAwal - $item['qty']);

                if ($stok) {
                    $stok->stok_sekarang = $saldoAkhir;
                    $stok->save();
                }

                // Create KartuStok record
                \App\Models\KartuStok::create([
                    'tanggal' => $request->tanggal_keluar . ' ' . date('H:i:s'),
                    'kode_gudang' => $request->gudang_asal_kode,
                    'barang_id' => $item['barang_id'],
                    'saldo_awal' => $saldoAwal,
                    'masuk' => 0,
                    'keluar' => $item['qty'],
                    'saldo_akhir' => $saldoAkhir,
                    'barang_keluar_id' => $barangKeluar->id,
                ]);
            }
        });

        return redirect()->route('barang-keluar.index')->with('success', 'Transaksi barang keluar berhasil dicatat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BarangKeluar $barangKeluar)
    {
        $barangKeluar->load(['gudang', 'user', 'details.barang.satuan']);
        return view('barang_keluar.show', compact('barangKeluar'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BarangKeluar $barangKeluar)
    {
        DB::transaction(function() use ($barangKeluar) {
            // Revert stocks: increment back
            foreach ($barangKeluar->details as $detail) {
                $stok = StokGudang::firstOrNew([
                    'kode_gudang' => $barangKeluar->gudang_asal_kode,
                    'barang_id' => $detail->barang_id
                ]);
                
                $saldoAwal = $stok->stok_sekarang ?: 0;
                $saldoAkhir = $saldoAwal + $detail->qty;
                
                $stok->stok_sekarang = $saldoAkhir;
                $stok->save();

                // Record reversion in kartu_stoks
                \App\Models\KartuStok::create([
                    'tanggal' => now(),
                    'kode_gudang' => $barangKeluar->gudang_asal_kode,
                    'barang_id' => $detail->barang_id,
                    'saldo_awal' => $saldoAwal,
                    'masuk' => $detail->qty, // revert keluar by adding it back
                    'keluar' => 0,
                    'saldo_akhir' => $saldoAkhir,
                ]);
            }

            // Cascade delete details and parent transaction record
            $barangKeluar->delete();
        });

        return redirect()->route('barang-keluar.index')->with('success', 'Transaksi barang keluar berhasil dihapus dan stok telah disesuaikan kembali!');
    }
}
