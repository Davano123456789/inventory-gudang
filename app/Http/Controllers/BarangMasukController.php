<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\DetailBarangMasuk;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\StokGudang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeGudang = auth()->user()->getActiveGudangCode();
        $query = BarangMasuk::with(['gudang', 'user']);

        if ($activeGudang !== 'all') {
            $query->where(function($q) use ($activeGudang) {
                $q->where('gudang_tujuan_kode', $activeGudang)
                  ->orWhere('gudang_asal_kode', $activeGudang);
            });
        }

        $transactions = $query->orderBy('tanggal_masuk', 'desc')->get();
        return view('barang_masuk.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $activeGudang = $user->getActiveGudangCode();
        $barangs = Barang::with('satuan')->orderBy('nama_barang', 'asc')->get();
        
        $gudangTujuans = Gudang::where('kode_gudang', $activeGudang)->get();

        $gudangs = Gudang::orderBy('nama_gudang', 'asc')->get();
        $satuans = \App\Models\Satuan::orderBy('nama_satuan', 'asc')->get();
        return view('barang_masuk.create', compact('barangs', 'gudangs', 'gudangTujuans', 'satuans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_surat_jalan' => 'required|string|max:100|unique:barang_masuks,no_surat_jalan',
            'tanggal_masuk' => 'required|date',
            'tanggal_surat_jalan' => 'required|date',
            'jenis_transaksi' => 'required|string|in:Biasa,Retur,Mutasi',
            'gudang_asal_kode' => 'required_if:jenis_transaksi,Mutasi|nullable|exists:gudangs,kode_gudang',
            'pengirim' => 'nullable|string|max:150',
            'gudang_tujuan_kode' => 'required|exists:gudangs,kode_gudang',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.satuan_id' => 'nullable|exists:satuans,id',
            'items.*.qty_box' => 'nullable|numeric|min:0',
            'items.*.qty_pcs' => 'nullable|numeric|min:0',
            'items.*.qty_total' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function() use ($request) {
            // 1. Create the main BarangMasuk
            $barangMasuk = BarangMasuk::create([
                'no_surat_jalan' => $request->no_surat_jalan,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_surat_jalan' => $request->tanggal_surat_jalan,
                'jenis_transaksi' => $request->jenis_transaksi,
                'gudang_asal_kode' => $request->jenis_transaksi === 'Mutasi' ? $request->gudang_asal_kode : null,
                'pengirim' => $request->pengirim,
                'gudang_tujuan_kode' => $request->gudang_tujuan_kode,
                'user_id' => Auth::id() ?: 1, // Default to first user if auth is missing for development
                'catatan' => $request->catatan,
            ]);

            // 2. Create each detail and increment/decrement warehouse stock
            foreach ($request->items as $item) {
                $qtyBox = $item['qty_box'] ?: 0;
                $qtyPcs = $item['qty_pcs'] ?: 0;
                $qtyTotal = $item['qty_total'];

                // Auto-update item's unit if it doesn't have one and unit is provided
                if (!empty($item['satuan_id'])) {
                    $barangObj = Barang::find($item['barang_id']);
                    if ($barangObj && is_null($barangObj->satuan_id)) {
                        $barangObj->update(['satuan_id' => $item['satuan_id']]);
                    }
                }

                DetailBarangMasuk::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'barang_id' => $item['barang_id'],
                    'qty_box' => $qtyBox,
                    'qty_pcs' => $qtyPcs,
                    'qty_total' => $qtyTotal,
                ]);

                // If transaction is Mutasi, decrement stock from source warehouse
                if ($request->jenis_transaksi === 'Mutasi') {
                    $stokAsal = StokGudang::firstOrNew([
                        'kode_gudang' => $request->gudang_asal_kode,
                        'barang_id' => $item['barang_id']
                    ]);
                    $stokAsal->stok_sekarang = ($stokAsal->stok_sekarang ?: 0) - $qtyTotal;
                    $stokAsal->save();
                }

                // Always increment stock in destination warehouse
                $stokTujuan = StokGudang::firstOrNew([
                    'kode_gudang' => $request->gudang_tujuan_kode,
                    'barang_id' => $item['barang_id']
                ]);
                $stokTujuan->stok_sekarang = ($stokTujuan->stok_sekarang ?: 0) + $qtyTotal;
                $stokTujuan->save();
            }
        });

        return redirect()->route('barang-masuk.index')->with('success', 'Transaksi barang masuk berhasil dicatat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BarangMasuk $barangMasuk)
    {
        $barangMasuk->load(['gudang', 'user', 'details.barang.satuan']);
        return view('barang_masuk.show', compact('barangMasuk'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BarangMasuk $barangMasuk)
    {
        DB::transaction(function() use ($barangMasuk) {
            // Decrement target stocks and increment source stocks before deleting the records
            foreach ($barangMasuk->details as $detail) {
                // Revert source warehouse stock (increment it back) if Mutasi
                if ($barangMasuk->jenis_transaksi === 'Mutasi' && $barangMasuk->gudang_asal_kode) {
                    $stokAsal = StokGudang::where('kode_gudang', $barangMasuk->gudang_asal_kode)
                                          ->where('barang_id', $detail->barang_id)
                                          ->first();
                    if ($stokAsal) {
                        $stokAsal->stok_sekarang = ($stokAsal->stok_sekarang ?: 0) + $detail->qty_total;
                        $stokAsal->save();
                    }
                }

                // Revert target warehouse stock (decrement it)
                $stokTujuan = StokGudang::where('kode_gudang', $barangMasuk->gudang_tujuan_kode)
                                        ->where('barang_id', $detail->barang_id)
                                        ->first();
                if ($stokTujuan) {
                    $stokTujuan->stok_sekarang = max(0, $stokTujuan->stok_sekarang - $detail->qty_total);
                    $stokTujuan->save();
                }
            }

            // Cascade delete details and parent transaction record
            $barangMasuk->delete();
        });

        return redirect()->route('barang-masuk.index')->with('success', 'Transaksi barang masuk berhasil dihapus dan stok telah disesuaikan kembali!');
    }
}
