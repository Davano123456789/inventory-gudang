<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GudangController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;

// Guest Auth Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated Scoped Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);

    Route::resource('gudang', GudangController::class);
    Route::resource('satuan', SatuanController::class);
    Route::get('barang-manual', [BarangController::class, 'manualIndex'])->name('barang-manual.index');
    Route::get('barang-manual/create', [BarangController::class, 'manualCreate'])->name('barang-manual.create');
    Route::post('barang-manual', [BarangController::class, 'manualStore'])->name('barang-manual.store');
    
    Route::resource('barang', BarangController::class);
    Route::post('barang/import', [BarangController::class, 'import'])->name('barang.import');
    
    // Mutasi Approvals
    Route::post('mutasi/{id}/approve', [App\Http\Controllers\BarangMasukController::class, 'approveMutasi'])->name('mutasi.approve');
    Route::post('mutasi/{id}/reject', [App\Http\Controllers\BarangMasukController::class, 'rejectMutasi'])->name('mutasi.reject');

    Route::resource('barang-masuk', App\Http\Controllers\BarangMasukController::class);
    Route::resource('barang-keluar', BarangKeluarController::class);

    Route::get('stock-opname/get-warehouse-items', [StockOpnameController::class, 'getWarehouseItems'])->name('stock-opname.warehouse-items');
    Route::resource('stock-opname', StockOpnameController::class);

    // Laporan
    Route::get('laporan/rekap', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.rekap');

    Route::post('switch-gudang', function(\Illuminate\Http\Request $request) {
        $request->validate([
            'kode_gudang' => 'required'
        ]);
        session(['active_gudang_kode' => $request->kode_gudang]);
        return redirect()->back()->with('success', 'Gudang aktif berhasil dialihkan!');
    })->name('switch-gudang');

    Route::resource('user', UserController::class);
});
