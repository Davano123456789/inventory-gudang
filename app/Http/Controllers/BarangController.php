<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Satuan;
use App\Models\Gudang;
use App\Models\StokGudang;
use Illuminate\Http\Request;
use Shuchkin\SimpleXLSX;

class BarangController extends Controller
{
    /**
     * Restrict write operations (including Excel imports) to Super Admin.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $action = $request->route() ? $request->route()->getActionMethod() : '';
            
            if ($user && !$user->isSuperAdmin() && in_array($action, ['edit', 'update', 'destroy'])) {
                $barang = $request->route()->parameter('barang');
                if (is_numeric($barang)) {
                    $barang = \App\Models\Barang::find($barang);
                }
                if ($barang && $barang->created_by_user_id != $user->id) {
                    abort(403, 'Akses ditolak. Anda bukan pembuat barang ini.');
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        $user = auth()->user();
        $gudangs = Gudang::all();
        $activeGudang = $user->getActiveGudangCode();

        $query = Barang::with(['satuan', 'stokGudangs']);

        if ($user->isSuperAdmin()) {
            // Super Admin: strictly filter by selected active warehouse
            if ($activeGudang) {
                $query->whereHas('stokGudangs', function ($q) use ($activeGudang) {
                    $q->where('kode_gudang', $activeGudang);
                });
            }
        } else {
            // Kepala Gudang: show items in their warehouse OR items they created
            $query->where(function ($q) use ($user, $activeGudang) {
                $q->where('created_by_user_id', $user->id);
                if ($activeGudang) {
                    $q->orWhereHas('stokGudangs', function ($sq) use ($activeGudang) {
                        $sq->where('kode_gudang', $activeGudang);
                    });
                }
            });
        }

        if ($activeGudang && $activeGudang !== 'all') {
            $query->select('barangs.*')
                ->selectSub(function ($q) use ($activeGudang) {
                    $q->from('stok_gudangs')
                      ->select('stok_sekarang')
                      ->whereColumn('stok_gudangs.barang_id', 'barangs.id')
                      ->where('stok_gudangs.kode_gudang', $activeGudang)
                      ->limit(1);
                }, 'current_stok')
                ->orderByRaw('COALESCE(current_stok, 0) ASC')
                ->orderBy('barangs.id', 'desc');
        } else {
            $query->orderBy('stok_global', 'asc')
                ->orderBy('barangs.id', 'desc');
        }

        $barangs = $query->get();

        return view('barang.index', compact('barangs', 'gudangs'));
    }

    public function create()
    {
        $satuans = Satuan::all();
        $gudangs = Gudang::all();
        return view('barang.create', compact('satuans', 'gudangs'));
    }

    public function manualIndex()
    {
        $user = auth()->user();
        $activeGudang = $user->getActiveGudangCode();
        $gudangs = Gudang::orderBy('nama_gudang', 'asc')->get();
        
        $query = Barang::with(['satuan', 'stokGudangs']);
        
        if ($user->isSuperAdmin()) {
            if ($activeGudang && $activeGudang !== 'all') {
                $query->whereHas('stokGudangs', function ($q) use ($activeGudang) {
                    $q->where('kode_gudang', $activeGudang);
                });
            }
        } else {
            $query->where(function ($q) use ($user, $activeGudang) {
                $q->where('created_by_user_id', $user->id);
                if ($activeGudang && $activeGudang !== 'all') {
                    $q->orWhereHas('stokGudangs', function ($sq) use ($activeGudang) {
                        $sq->where('kode_gudang', $activeGudang);
                    });
                }
            });
        }

        $barangs = $query->orderBy('created_at', 'desc')->get();
        
        return view('barang.manual_index', compact('barangs', 'gudangs'));
    }

    public function manualCreate()
    {
        $satuans = Satuan::all();
        $gudangs = Gudang::all();
        return view('barang.manual_create', compact('satuans', 'gudangs'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'kode_barang' => 'required|string|max:100|unique:barangs,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'satuan_id' => 'nullable|exists:satuans,id',
            'kode_gudang' => $user->isSuperAdmin() ? 'nullable|exists:gudangs,kode_gudang' : '',
            'stok_awal' => 'nullable|numeric|min:0',
        ]);

        $gudangCode = $user->isSuperAdmin() 
            ? ($request->kode_gudang ?: ($user->getActiveGudangCode() !== 'all' ? $user->getActiveGudangCode() : null)) 
            : $user->getActiveGudangCode();

        $barang = Barang::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'satuan_id' => $request->satuan_id,
            'size_id' => $request->input('size_id', 1),
            'created_by_user_id' => $user->id,
            'gudang_pendaftar_kode' => $gudangCode,
        ]);

        // Global Sync: Create stok_gudang for ALL warehouses
        $gudangs = \App\Models\Gudang::all();
        foreach ($gudangs as $gudang) {
            $isTarget = ($gudang->kode_gudang == $gudangCode);
            StokGudang::create([
                'kode_gudang' => $gudang->kode_gudang,
                'barang_id' => $barang->id,
                'size_id' => 1,
                'stok_sekarang' => $isTarget ? $stokAwal : 0
            ]);

            // Only record KartuStok if it's the target warehouse and there's initial stock
            if ($isTarget && $stokAwal > 0) {
                \App\Models\KartuStok::create([
                    'tanggal' => now()->toDateString(),
                    'kode_gudang' => $gudangCode,
                    'barang_id' => $barang->id,
                    'saldo_awal' => 0,
                    'masuk' => $stokAwal,
                    'keluar' => 0,
                    'saldo_akhir' => $stokAwal,
                    'keterangan' => 'Stok Awal (Master)'
                ]);
            }
        }

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Barang $barang)
    {
        $barang->load('satuan');
        return view('barang.show', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barang $barang)
    {
        $satuans = Satuan::all();
        return view('barang.edit', compact('barang', 'satuans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:100|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:255',
            'satuan_id' => 'nullable|exists:satuans,id',
        ]);

        $barang->update($request->all());

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    {
        // In the future, we can add checks here to prevent deleting if the item has transactions
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
    }

    public function manualStore(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'kode_barang' => 'required|string|max:100|unique:barangs,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'satuan_id' => 'nullable|exists:satuans,id',
            'kode_gudang' => 'required|exists:gudangs,kode_gudang',
            'stok_awal' => 'nullable|numeric|min:0',
        ]);

        $gudangCode = $request->input('kode_gudang');
        $stokAwal = floatval($request->input('stok_awal', 0));

        // Create new global master
        $barang = Barang::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'satuan_id' => $request->satuan_id,
            'created_by_user_id' => $user->id,
            'gudang_pendaftar_kode' => $gudangCode,
        ]);

        // Global Sync: Create stok_gudang for ALL warehouses
        $gudangs = \App\Models\Gudang::all();
        foreach ($gudangs as $gudang) {
            $isTarget = ($gudang->kode_gudang == $gudangCode);
            $barang->stokGudangs()->create([
                'kode_gudang' => $gudang->kode_gudang,
                'stok_sekarang' => $isTarget ? $stokAwal : 0,
            ]);

            // Record transaction in kartu_stoks ONLY for target warehouse if stock > 0
            if ($isTarget && $stokAwal > 0) {
                \App\Models\KartuStok::create([
                    'tanggal' => now()->toDateString(),
                    'kode_gudang' => $gudangCode,
                    'barang_id' => $barang->id,
                    'saldo_awal' => 0,
                    'masuk' => $stokAwal,
                    'keluar' => 0,
                    'saldo_akhir' => $stokAwal,
                    'keterangan' => 'Stok Awal (Manual)'
                ]);
            }
        }

        return redirect()->route('barang-manual.index')->with('success', 'Barang manual berhasil ditambahkan dan disinkronisasi ke seluruh gudang.');
    }

    public function import(Request $request)
    {
        $user = auth()->user();
        $rules = [
            'file' => 'required|file|mimes:xlsx',
            'kode_gudang' => 'required|exists:gudangs,kode_gudang',
        ];
        $request->validate($rules);

        $file = $request->file('file');
        $selectedGudangCode = $request->input('kode_gudang');

        // Find the selected warehouse
        $gudang = Gudang::where('kode_gudang', $selectedGudangCode)->firstOrFail();

        if ($xlsx = SimpleXLSX::parse($file->getPathname())) {
            $rows = $xlsx->rows();
            
            $headerFound = false;
            $importedCount = 0;
            
            foreach ($rows as $row) {
                // If we haven't found the table header yet, check if this row looks like the header
                if (!$headerFound) {
                    $rowStr = implode(' ', array_map('strval', $row));
                    if (str_contains($rowStr, 'Kode') && str_contains($rowStr, 'Nama Barang')) {
                        $headerFound = true;
                    }
                    continue;
                }
                
                // Process data rows
                $kodeBarang = isset($row[1]) ? trim($row[1]) : '';
                $namaBarang = isset($row[3]) ? trim($row[3]) : '';
                $saldoAkhirStr = isset($row[4]) ? trim($row[4]) : '0';
                
                if (empty($kodeBarang) || empty($namaBarang)) {
                    continue;
                }
                
                // 1. Create the Barang record if it doesn't exist, else fetch it
                $barang = Barang::where('kode_barang', $kodeBarang)->first();
                if (!$barang) {
                    $barang = Barang::create([
                        'kode_barang' => $kodeBarang,
                        'nama_barang' => $namaBarang,
                        'satuan_id' => null,
                        'size_id' => 1,
                        'created_by_user_id' => $user->id,
                        'gudang_pendaftar_kode' => 'excel'
                    ]);
                }
                
                // 2. Initialize stock balance for the selected warehouse
                // Clean and parse the Saldo Akhir decimal (e.g. "1.900,00" or "1,900.00" or "1900")
                $cleanSaldo = preg_replace('/[^0-9,.-]/', '', $saldoAkhirStr);
                if (strpos($cleanSaldo, '.') !== false && strpos($cleanSaldo, ',') !== false) {
                    if (strpos($cleanSaldo, '.') < strpos($cleanSaldo, ',')) {
                        // 1.900,00 -> dot is thousands, comma is decimal
                        $cleanSaldo = str_replace('.', '', $cleanSaldo);
                        $cleanSaldo = str_replace(',', '.', $cleanSaldo);
                    } else {
                        // 1,900.00 -> comma is thousands, dot is decimal
                        $cleanSaldo = str_replace(',', '', $cleanSaldo);
                    }
                } else {
                    if (strpos($cleanSaldo, ',') !== false) {
                        if (preg_match('/,\d{2}$/', $cleanSaldo)) {
                            $cleanSaldo = str_replace(',', '.', $cleanSaldo);
                        } else {
                            $cleanSaldo = str_replace(',', '', $cleanSaldo);
                        }
                    }
                }
                
                $saldoDecimal = floatval($cleanSaldo);
                
                // Read current stock balance before update
                $existingStok = StokGudang::where('kode_gudang', $gudang->kode_gudang)
                    ->where('barang_id', $barang->id)
                    ->first();
                $saldoAwal = $existingStok ? $existingStok->stok_sekarang : 0;

                // Create or Update Stok Gudang record for the selected warehouse
                StokGudang::updateOrCreate(
                    [
                        'kode_gudang' => $gudang->kode_gudang,
                        'barang_id' => $barang->id
                    ],
                    [
                        'stok_sekarang' => $saldoDecimal
                    ]
                );

                // Record transaction in kartu_stoks
                \App\Models\KartuStok::create([
                    'tanggal' => now()->toDateString(),
                    'kode_gudang' => $gudang->kode_gudang,
                    'barang_id' => $barang->id,
                    'saldo_awal' => $saldoAwal,
                    'masuk' => $saldoDecimal - $saldoAwal > 0 ? $saldoDecimal - $saldoAwal : 0,
                    'keluar' => $saldoAwal - $saldoDecimal > 0 ? $saldoAwal - $saldoDecimal : 0,
                    'saldo_akhir' => $saldoDecimal,
                    'keterangan' => 'Import Excel'
                ]);
                
                $importedCount++;
            }
            
            if ($importedCount > 0) {
                return redirect()->route('barang.index')->with('success', $importedCount . ' data barang dan stok berhasil di-import ke ' . $gudang->nama_gudang . '!');
            } else {
                return redirect()->route('barang.index')->with('error', 'Tidak ada data barang yang valid ditemukan di dalam file Excel.');
            }
        } else {
            return redirect()->route('barang.index')->with('error', 'Gagal memproses file Excel: ' . SimpleXLSX::parseError());
        }
    }
}
