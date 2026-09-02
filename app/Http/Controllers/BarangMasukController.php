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
            $query->where('gudang_tujuan_kode', $activeGudang);
        }

        $transactions = $query->orderBy('tanggal_masuk', 'asc')->get();

        // Pending Mutasi Query
        $pendingMutasiQuery = \App\Models\BarangKeluar::with(['gudang', 'user', 'details.barang'])
            ->where('jenis', \App\Models\BarangKeluar::JENIS_MUTASI)
            ->where('status', \App\Models\BarangKeluar::STATUS_PENDING);
            
        if ($activeGudang !== 'all') {
            $pendingMutasiQuery->where('gudang_tujuan_kode', $activeGudang);
        }
        
        $pendingMutasi = $pendingMutasiQuery->orderBy('tanggal_keluar', 'asc')->get();

        return view('barang_masuk.index', compact('transactions', 'pendingMutasi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $activeGudang = $user->getActiveGudangCode();
        $barangs = Barang::with(['satuan', 'stokGudangs'])->orderBy('nama_barang', 'asc')->get();
        
        $gudangTujuans = Gudang::where('kode_gudang', $activeGudang)->get();
        if ($activeGudang === 'all') {
            $gudangTujuans = Gudang::orderBy('nama_gudang', 'asc')->get();
        }

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
            'jenis_transaksi' => 'required|string|in:Reguler,Biasa,Return,Retur,Mutasi,Stock Opname',
            'gudang_asal_kode' => 'required_if:jenis_transaksi,Mutasi|nullable|exists:gudangs,kode_gudang',
            'pengirim' => 'nullable|string|max:150',
            'gudang_tujuan_kode' => 'required|exists:gudangs,kode_gudang',
            'catatan' => 'nullable|string',
            'items' => 'required_without:new_items|array',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.satuan_id' => 'nullable|exists:satuans,id',
            'items.*.qty_box' => 'nullable|numeric|min:0',
            'items.*.qty_pcs' => 'nullable|numeric|min:0',
            'items.*.qty_total' => 'required|numeric|min:0.01',
            'new_items' => 'required_without:items|array',
            'new_items.*.kode_barang' => 'required|string|max:100|unique:barangs,kode_barang',
            'new_items.*.nama_barang' => 'required|string|max:255',
            'new_items.*.satuan_id' => 'required|exists:satuans,id',
            'new_items.*.qty_box' => 'nullable|numeric|min:0',
            'new_items.*.qty_pcs' => 'nullable|numeric|min:0',
            'new_items.*.qty_total' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function() use ($request) {
            // 1. Create the main BarangMasuk
            $jenisTrans = match($request->jenis_transaksi) {
                'Mutasi' => \App\Models\BarangMasuk::JENIS_MUTASI,
                'Retur', 'Return' => \App\Models\BarangMasuk::JENIS_RETURN,
                'Stock Opname' => \App\Models\BarangMasuk::JENIS_STOCK_OPNAME,
                default => \App\Models\BarangMasuk::JENIS_REGULER,
            };

            $barangMasuk = BarangMasuk::create([
                'no_surat_jalan' => $request->no_surat_jalan,
                'tanggal_masuk' => $request->tanggal_masuk,
                'tanggal_surat_jalan' => $request->tanggal_surat_jalan,
                'jenis_transaksi' => $jenisTrans,
                'gudang_asal_kode' => $request->jenis_transaksi === 'Mutasi' ? $request->gudang_asal_kode : null,
                'pengirim' => $request->pengirim,
                'gudang_tujuan_kode' => $request->gudang_tujuan_kode,
                'user_id' => Auth::id() ?: 1, // Default to first user if auth is missing for development
                'catatan' => $request->catatan,
            ]);

            $allItems = [];
            
            // Collect existing items
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $allItems[] = $item;
                }
            }

            // Collect and process new items
            if ($request->has('new_items') && is_array($request->new_items)) {
                foreach ($request->new_items as $newItem) {
                    $newBarang = \App\Models\Barang::create([
                        'kode_barang' => $newItem['kode_barang'],
                        'nama_barang' => $newItem['nama_barang'],
                        'satuan_id' => $newItem['satuan_id'],
                        'created_by_user_id' => Auth::id() ?: 1,
                        'gudang_pendaftar_kode' => $request->gudang_tujuan_kode,
                    ]);

                    // Sync to all warehouses with 0 stock
                    $gudangs = \App\Models\Gudang::all();
                    foreach ($gudangs as $gudang) {
                        $newBarang->stokGudangs()->create([
                            'kode_gudang' => $gudang->kode_gudang,
                            'stok_sekarang' => 0,
                        ]);
                    }

                    $allItems[] = [
                        'barang_id' => $newBarang->id,
                        'satuan_id' => $newItem['satuan_id'],
                        'qty_box' => $newItem['qty_box'] ?? 0,
                        'qty_pcs' => $newItem['qty_pcs'] ?? 0,
                        'qty_total' => $newItem['qty_total'],
                    ];
                }
            }

            // 2. Create each detail and increment/decrement warehouse stock
            foreach ($allItems as $item) {

                $qtyBox = $item['qty_box'] ?? 0;
                $qtyPcs = $item['qty_pcs'] ?? 0;
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
                    'size_id' => $item['size_id'] ?? 1,
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
                    $saldoAwalAsal = $stokAsal->stok_sekarang ?: 0;
                    $saldoAkhirAsal = $saldoAwalAsal - $qtyTotal;

                    $stokAsal->stok_sekarang = $saldoAkhirAsal;
                    $stokAsal->save();

                    // Record in kartu_stoks for source warehouse
                    \App\Models\KartuStok::create([
                        'tanggal' => $request->tanggal_masuk . ' ' . date('H:i:s'),
                        'kode_gudang' => $request->gudang_asal_kode,
                        'barang_id' => $item['barang_id'],
                        'saldo_awal' => $saldoAwalAsal,
                        'masuk' => 0,
                        'keluar' => $qtyTotal,
                        'saldo_akhir' => $saldoAkhirAsal,
                        'barang_masuk_id' => $barangMasuk->id,
                    ]);
                }

                // Always increment stock in destination warehouse
                $stokTujuan = StokGudang::firstOrNew([
                    'kode_gudang' => $request->gudang_tujuan_kode,
                    'barang_id' => $item['barang_id']
                ]);
                $saldoAwalTujuan = $stokTujuan->stok_sekarang ?: 0;
                $saldoAkhirTujuan = $saldoAwalTujuan + $qtyTotal;

                $stokTujuan->stok_sekarang = $saldoAkhirTujuan;
                $stokTujuan->save();

                // Record in kartu_stoks for destination warehouse
                \App\Models\KartuStok::create([
                    'tanggal' => $request->tanggal_masuk . ' ' . date('H:i:s'),
                    'kode_gudang' => $request->gudang_tujuan_kode,
                    'barang_id' => $item['barang_id'],
                    'saldo_awal' => $saldoAwalTujuan,
                    'masuk' => $qtyTotal,
                    'keluar' => 0,
                    'saldo_akhir' => $saldoAkhirTujuan,
                    'barang_masuk_id' => $barangMasuk->id,
                    'keterangan' => $request->jenis_transaksi === 'Stock Opname' ? ('Penyesuaian Stock Opname: ' . ($request->catatan ?: $request->no_surat_jalan)) : null,
                ]);
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

    /**
     * Approve incoming mutation.
     */
    public function approveMutasi($id)
    {
        $mutasi = \App\Models\BarangKeluar::findOrFail($id);
        
        if ($mutasi->status != \App\Models\BarangKeluar::STATUS_PENDING || $mutasi->jenis != \App\Models\BarangKeluar::JENIS_MUTASI) {
            return redirect()->back()->with('error', 'Transaksi mutasi ini sudah tidak dapat di-approve.');
        }

        if (auth()->user()->role === 'staff_gudang') {
            return redirect()->back()->with('error', 'Staff Gudang tidak memiliki hak untuk menyetujui mutasi.');
        }

        $activeGudang = auth()->user()->getActiveGudangCode();
        if ($activeGudang !== 'all' && $activeGudang !== $mutasi->gudang_tujuan_kode) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menyetujui mutasi ini.');
        }
        
        DB::transaction(function() use ($mutasi) {
            $mutasi->status = \App\Models\BarangKeluar::STATUS_COMPLETED;
            $mutasi->approved_by = auth()->id();
            $mutasi->approved_at = now();
            $mutasi->save();
            
            // Create Barang Masuk history record
            $barangMasuk = \App\Models\BarangMasuk::create([
                'no_surat_jalan' => $mutasi->no_surat_jalan . '-IN',
                'tanggal_masuk' => date('Y-m-d'),
                'tanggal_surat_jalan' => $mutasi->tanggal_surat_jalan,
                'jenis_transaksi' => \App\Models\BarangMasuk::JENIS_MUTASI,
                'status' => \App\Models\BarangMasuk::STATUS_COMPLETED,
                'gudang_asal_kode' => $mutasi->gudang_asal_kode,
                'pengirim' => $mutasi->user ? $mutasi->user->name : 'Gudang Pengirim',
                'gudang_tujuan_kode' => $mutasi->gudang_tujuan_kode,
                'user_id' => auth()->id() ?: 1,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'catatan' => 'Penerimaan Mutasi Otomatis dari ' . $mutasi->no_surat_jalan,
            ]);

            foreach ($mutasi->details as $detail) {
                // Add detail
                \App\Models\DetailBarangMasuk::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'barang_id' => $detail->barang_id,
                    'qty_box' => 0,
                    'qty_pcs' => 0,
                    'qty_total' => $detail->qty,
                ]);

                // Update stock
                $stokTujuan = StokGudang::firstOrNew([
                    'kode_gudang' => $mutasi->gudang_tujuan_kode,
                    'barang_id' => $detail->barang_id
                ]);
                $saldoAwalTujuan = $stokTujuan->stok_sekarang ?: 0;
                $saldoAkhirTujuan = $saldoAwalTujuan + $detail->qty;

                $stokTujuan->stok_sekarang = $saldoAkhirTujuan;
                $stokTujuan->save();

                // Update existing pending stock card
                $kartuPending = \App\Models\KartuStok::where('barang_keluar_id', $mutasi->id)
                    ->where('kode_gudang', $mutasi->gudang_tujuan_kode)
                    ->where('barang_id', $detail->barang_id)
                    ->where('status', \App\Models\KartuStok::STATUS_PENDING)
                    ->first();

                if ($kartuPending) {
                    $kartuPending->update([
                        'tanggal' => date('Y-m-d H:i:s'),
                        'saldo_awal' => $saldoAwalTujuan,
                        'saldo_akhir' => $saldoAkhirTujuan,
                        'barang_masuk_id' => $barangMasuk->id,
                        'status' => \App\Models\BarangMasuk::STATUS_COMPLETED,
                        'keterangan' => null
                    ]);
                } else {
                    // Fallback if not found
                    \App\Models\KartuStok::create([
                        'tanggal' => date('Y-m-d H:i:s'),
                        'kode_gudang' => $mutasi->gudang_tujuan_kode,
                        'barang_id' => $detail->barang_id,
                        'saldo_awal' => $saldoAwalTujuan,
                        'masuk' => $detail->qty,
                        'keluar' => 0,
                        'saldo_akhir' => $saldoAkhirTujuan,
                        'barang_masuk_id' => $barangMasuk->id,
                        'barang_keluar_id' => $mutasi->id,
                        'status' => \App\Models\BarangMasuk::STATUS_COMPLETED
                    ]);
                }
            }
        });
        
        return redirect()->back()->with('success', 'Mutasi Surat Jalan ' . $mutasi->no_surat_jalan . ' berhasil diterima. Stok telah ditambahkan ke gudang Anda.');
    }

    /**
     * Reject incoming mutation.
     */
    public function rejectMutasi($id)
    {
        $mutasi = \App\Models\BarangKeluar::findOrFail($id);
        
        if ($mutasi->status != \App\Models\BarangKeluar::STATUS_PENDING || $mutasi->jenis != \App\Models\BarangKeluar::JENIS_MUTASI) {
            return redirect()->back()->with('error', 'Transaksi mutasi ini sudah tidak dapat ditolak.');
        }

        if (auth()->user()->role === 'staff_gudang') {
            return redirect()->back()->with('error', 'Staff Gudang tidak memiliki hak untuk menolak mutasi.');
        }
        
        $activeGudang = auth()->user()->getActiveGudangCode();
        if ($activeGudang !== 'all' && $activeGudang !== $mutasi->gudang_tujuan_kode) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menolak mutasi ini.');
        }

        DB::transaction(function() use ($mutasi) {
            $mutasi->status = \App\Models\BarangKeluar::STATUS_REJECTED;
            $mutasi->approved_by = auth()->id();
            $mutasi->approved_at = now();
            $mutasi->save();
            
            // Create Barang Masuk as "Mutasi" with status "rejected" for historical record
            $barangMasuk = \App\Models\BarangMasuk::create([
                'no_surat_jalan' => $mutasi->no_surat_jalan . '-REJ',
                'tanggal_masuk' => date('Y-m-d'),
                'tanggal_surat_jalan' => $mutasi->tanggal_keluar,
                'jenis_transaksi' => \App\Models\BarangMasuk::JENIS_MUTASI,
                'status' => \App\Models\BarangMasuk::STATUS_REJECTED,
                'gudang_asal_kode' => $mutasi->gudang_asal_kode,
                'pengirim' => $mutasi->user ? $mutasi->user->name : 'Gudang Pengirim',
                'gudang_tujuan_kode' => $mutasi->gudang_tujuan_kode,
                'user_id' => auth()->id(),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'catatan' => 'Penolakan Mutasi dari ' . $mutasi->no_surat_jalan,
            ]);
            
            // Revert stock back to source warehouse
            foreach ($mutasi->details as $detail) {
                // Delete pending stock card in destination warehouse
                \App\Models\KartuStok::where('barang_keluar_id', $mutasi->id)
                    ->where('kode_gudang', $mutasi->gudang_tujuan_kode)
                    ->where('barang_id', $detail->barang_id)
                    ->where('status', \App\Models\KartuStok::STATUS_PENDING)
                    ->delete();

                // Add detail to barang masuk for history
                \App\Models\DetailBarangMasuk::create([
                    'barang_masuk_id' => $barangMasuk->id,
                    'barang_id' => $detail->barang_id,
                    'qty_box' => $detail->qty_box ?? 0,
                    'qty_pcs' => $detail->qty_pcs ?? 0,
                    'qty_total' => $detail->qty,
                ]);

                $stokAsal = StokGudang::where('kode_gudang', $mutasi->gudang_asal_kode)
                                      ->where('barang_id', $detail->barang_id)
                                      ->first();
                if ($stokAsal) {
                    $saldoAwalAsal = $stokAsal->stok_sekarang ?: 0;
                    $saldoAkhirAsal = $saldoAwalAsal + $detail->qty;
                    $stokAsal->stok_sekarang = $saldoAkhirAsal;
                    $stokAsal->save();

                    \App\Models\KartuStok::create([
                        'tanggal' => date('Y-m-d H:i:s'),
                        'kode_gudang' => $mutasi->gudang_asal_kode,
                        'barang_id' => $detail->barang_id,
                        'saldo_awal' => $saldoAwalAsal,
                        'masuk' => $detail->qty,
                        'keluar' => 0,
                        'saldo_akhir' => $saldoAkhirAsal,
                        'barang_keluar_id' => $mutasi->id, // link to original transaction
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Mutasi Surat Jalan ' . $mutasi->no_surat_jalan . ' ditolak. Stok telah dikembalikan ke Gudang Asal.');
    }
}
