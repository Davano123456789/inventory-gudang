<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\StokGudang;
use App\Models\StockOpname;
use App\Models\DetailStockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeGudang = auth()->user()->getActiveGudangCode();
        $query = StockOpname::with(['gudang', 'user']);

        if ($activeGudang !== 'all') {
            $query->where('gudang_kode', $activeGudang);
        }

        $opnames = $query->orderBy('tanggal_opname', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();
        $gudangs = Gudang::orderBy('nama_gudang', 'asc')->get();
        return view('stock_opname.index', compact('opnames', 'gudangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $activeGudang = $user->getActiveGudangCode();
        
        $gudangs = Gudang::where('kode_gudang', $activeGudang)->get();

        return view('stock_opname.create', compact('gudangs'));
    }

    /**
     * Fetch all items and their current system stocks for the selected warehouse.
     */
    public function getWarehouseItems(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'gudang_kode' => 'required|exists:gudangs,kode_gudang',
        ]);

        if ($user->isKepalaGudang() && $request->gudang_kode !== $user->kode_gudang) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak ke gudang ini.'
            ], 403);
        }

        $gudangKode = $request->gudang_kode;

        $items = Barang::with('satuan')
            ->leftJoin('stok_gudangs', function($join) use ($gudangKode) {
                $join->on('barangs.id', '=', 'stok_gudangs.barang_id')
                     ->where('stok_gudangs.kode_gudang', '=', $gudangKode);
            })
            ->select('barangs.id', 'barangs.kode_barang', 'barangs.nama_barang', 'barangs.satuan_id', DB::raw('COALESCE(stok_gudangs.stok_sekarang, 0) as stok_sistem'))
            ->orderBy('barangs.nama_barang', 'asc')
            ->get();

        $formatted = $items->map(function($item) {
            return [
                'barang_id' => $item->id,
                'kode_barang' => $item->kode_barang,
                'nama_barang' => $item->nama_barang,
                'unit' => $item->satuan ? $item->satuan->nama_satuan : 'Unit',
                'stok_sistem' => floatval($item->stok_sistem),
            ];
        });

        return response()->json([
            'status' => 'success',
            'items' => $formatted
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('items_json')) {
            $items = json_decode($request->items_json, true) ?: [];
            $request->merge(['items' => $items]);
        }

        $request->validate([
            'tanggal_opname' => 'required|date',
            'gudang_kode' => 'required|exists:gudangs,kode_gudang',
            'status' => 'required|in:Draft,Selesai',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.stok_sistem' => 'required|numeric',
            'items.*.stok_fisik' => 'nullable|numeric|min:0',
            'items.*.keterangan' => 'nullable|string',
        ]);

        $gudangKode = $request->gudang_kode;
        $tanggalOpname = $request->tanggal_opname;
        $status = $request->status;
        $userId = Auth::id() ?: 1;

        // Generate dynamic No Opname
        $dateCode = now()->format('Ymd');
        $count = StockOpname::whereDate('created_at', now()->toDateString())->count() + 1;
        $noOpname = 'OP-' . $dateCode . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        DB::transaction(function() use ($request, $noOpname, $gudangKode, $tanggalOpname, $status, $userId) {
            // 1. Create Opname Header
            $opname = StockOpname::create([
                'no_opname' => $noOpname,
                'tanggal_opname' => $tanggalOpname,
                'gudang_kode' => $gudangKode,
                'status' => $status,
                'user_id' => $userId,
                'keterangan' => $request->keterangan,
            ]);

            // 2. Loop details
            foreach ($request->items as $itemData) {
                // Skip if not counted at all
                if (!isset($itemData['stok_fisik']) || $itemData['stok_fisik'] === '' || is_null($itemData['stok_fisik'])) {
                    continue;
                }

                $barangId = $itemData['barang_id'];
                $stokSistem = floatval($itemData['stok_sistem']);
                $stokFisik = floatval($itemData['stok_fisik']);
                $itemKeterangan = $itemData['keterangan'] ?? null;
                $selisih = $stokFisik - $stokSistem;

                // Create Detail
                DetailStockOpname::create([
                    'stock_opname_id' => $opname->id,
                    'barang_id' => $barangId,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'keterangan' => $itemKeterangan
                ]);

                // Catatan: Sesuai permintaan, fitur Stock Opname tidak lagi
                // otomatis mengubah/menimpa tabel stok_gudangs. 
                // Fitur ini murni hanya sebagai dokumen audit (laporan selisih).
                // Penyesuaian stok akan dilakukan manual via menu Barang Masuk/Keluar.
            }
        });

        $msg = $status === 'Selesai' 
            ? 'Dokumen Stock Opname berhasil diselesaikan (Stok gudang tidak diubah otomatis).' 
            : 'Draft stock opname berhasil disimpan!';

        return redirect()->route('stock-opname.index')->with('success', $msg);
    }

    /**
     * Display the specified resource.
     */
    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['gudang', 'user']);
        
        // Memuat detail barang dan mengurutkannya:
        // Yang punya selisih (bukan 0) akan ditaruh di paling atas.
        // Yang selisihnya 0 akan berada di bawah.
        $stockOpname->load(['details' => function ($query) {
            $query->orderByRaw('ABS(selisih) DESC');
        }, 'details.barang.satuan']);

        return view('stock_opname.show', compact('stockOpname'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockOpname $stockOpname)
    {
        // Block edit if document is already finalized (Posted)
        if ($stockOpname->status === 'Selesai') {
            return redirect()->route('stock-opname.show', $stockOpname->id)
                             ->with('error', 'Dokumen stock opname yang sudah selesai tidak dapat diedit!');
        }

        // Get details mapped to items key for easy lookup in blade
        $stockOpname->load(['details', 'gudang']);
        
        // Fetch all items from DB and left-join them with existing saved draft counts
        $items = Barang::with('satuan')
            ->leftJoin('stok_gudangs', function($join) use ($stockOpname) {
                $join->on('barangs.id', '=', 'stok_gudangs.barang_id')
                     ->where('stok_gudangs.kode_gudang', '=', $stockOpname->gudang_kode);
            })
            ->select('barangs.id', 'barangs.kode_barang', 'barangs.nama_barang', 'barangs.satuan_id', DB::raw('COALESCE(stok_gudangs.stok_sekarang, 0) as stok_sistem'))
            ->orderBy('barangs.nama_barang', 'asc')
            ->get();

        $formatted = $items->map(function($item) use ($stockOpname) {
            // Find if there's already a saved draft count for this item
            $detail = $stockOpname->details->firstWhere('barang_id', $item->id);
            return [
                'barang_id' => $item->id,
                'kode_barang' => $item->kode_barang,
                'nama_barang' => $item->nama_barang,
                'unit' => $item->satuan ? $item->satuan->nama_satuan : 'Unit',
                'stok_sistem' => floatval($item->stok_sistem),
                'stok_fisik' => $detail && !is_null($detail->stok_fisik) ? floatval($detail->stok_fisik) : null,
                'keterangan' => $detail ? $detail->keterangan : null,
            ];
        });

        return view('stock_opname.edit', compact('stockOpname', 'formatted'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->status === 'Selesai') {
            return redirect()->route('stock-opname.index')->with('error', 'Dokumen yang sudah selesai tidak dapat diubah!');
        }

        if ($request->has('items_json')) {
            $items = json_decode($request->items_json, true) ?: [];
            $request->merge(['items' => $items]);
        }

        $request->validate([
            'tanggal_opname' => 'required|date',
            'status' => 'required|in:Draft,Selesai',
            'keterangan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.stok_sistem' => 'required|numeric',
            'items.*.stok_fisik' => 'nullable|numeric|min:0',
            'items.*.keterangan' => 'nullable|string',
        ]);

        $status = $request->status;

        DB::transaction(function() use ($request, $stockOpname, $status) {
            // 1. Update Header
            $stockOpname->update([
                'tanggal_opname' => $request->tanggal_opname,
                'status' => $status,
                'keterangan' => $request->keterangan
            ]);

            // Delete old details to make way for updated ones
            $stockOpname->details()->delete();

            // 2. Insert new details
            foreach ($request->items as $itemData) {
                if (!isset($itemData['stok_fisik']) || $itemData['stok_fisik'] === '' || is_null($itemData['stok_fisik'])) {
                    continue;
                }

                $barangId = $itemData['barang_id'];
                $stokSistem = floatval($itemData['stok_sistem']);
                $stokFisik = floatval($itemData['stok_fisik']);
                $itemKeterangan = $itemData['keterangan'] ?? null;
                $selisih = $stokFisik - $stokSistem;

                DetailStockOpname::create([
                    'stock_opname_id' => $stockOpname->id,
                    'barang_id' => $barangId,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'keterangan' => $itemKeterangan
                ]);

                // If Selesai (Posted), update warehouse stocks in database
                if ($status === 'Selesai') {
                    $stok = StokGudang::where('kode_gudang', $stockOpname->gudang_kode)
                                      ->where('barang_id', $barangId)
                                      ->first();
                    if ($stok) {
                        $stok->stok_sekarang = $stokFisik;
                        $stok->save();
                    } else {
                        StokGudang::create([
                            'kode_gudang' => $stockOpname->gudang_kode,
                            'barang_id' => $barangId,
                            'stok_sekarang' => $stokFisik
                        ]);
                    }
                }
            }
        });

        $msg = $status === 'Selesai' 
            ? 'Stock opname berhasil diselesaikan dan stok gudang telah diperbarui!' 
            : 'Draft stock opname berhasil diperbarui!';

        return redirect()->route('stock-opname.index')->with('success', $msg);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockOpname $stockOpname)
    {
        DB::transaction(function() use ($stockOpname) {
            // Revert stock changes only if it was Posted (Selesai)
            if ($stockOpname->status === 'Selesai') {
                foreach ($stockOpname->details as $detail) {
                    $stok = StokGudang::where('kode_gudang', $stockOpname->gudang_kode)
                                      ->where('barang_id', $detail->barang_id)
                                      ->first();
                    if ($stok) {
                        $stok->stok_sekarang = max(0, $stok->stok_sekarang - $detail->selisih);
                        $stok->save();
                    }
                }
            }

            // Cascade delete details and parent transaction record
            $stockOpname->delete();
        });

        return redirect()->route('stock-opname.index')->with('success', 'Transaksi stock opname berhasil dihapus dan stok gudang disesuaikan!');
    }
}
