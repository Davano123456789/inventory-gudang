<?php
$files = [
    __DIR__ . '/app/Http/Controllers/BarangMasukController.php',
    __DIR__ . '/app/Http/Controllers/BarangKeluarController.php',
    __DIR__ . '/app/Http/Controllers/DashboardController.php',
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Status
    $content = str_replace("'status' => 'pending'", "'status' => \App\Models\BarangMasuk::STATUS_PENDING", $content);
    $content = str_replace("'status' => 'completed'", "'status' => \App\Models\BarangMasuk::STATUS_COMPLETED", $content);
    $content = str_replace("'status' => 'approved'", "'status' => \App\Models\BarangMasuk::STATUS_COMPLETED", $content);
    $content = str_replace("'status' => 'rejected'", "'status' => \App\Models\BarangMasuk::STATUS_REJECTED", $content);
    
    $content = str_replace("\$mutasi->status = 'approved'", "\$mutasi->status = \App\Models\BarangKeluar::STATUS_COMPLETED", $content);
    $content = str_replace("\$mutasi->status = 'rejected'", "\$mutasi->status = \App\Models\BarangKeluar::STATUS_REJECTED", $content);
    
    $content = str_replace("\$mutasi->status !== 'pending'", "\$mutasi->status != \App\Models\BarangKeluar::STATUS_PENDING", $content);
    $content = str_replace("\$mutasi->jenis !== 'mutasi'", "\$mutasi->jenis != \App\Models\BarangKeluar::JENIS_MUTASI", $content);

    $content = str_replace("where('status', 'pending')", "where('status', \App\Models\KartuStok::STATUS_PENDING)", $content);
    
    // Jenis
    $content = str_replace("'jenis_transaksi' => 'Mutasi'", "'jenis_transaksi' => \App\Models\BarangMasuk::JENIS_MUTASI", $content);
    $content = str_replace("'jenis_transaksi' => 'Biasa'", "'jenis_transaksi' => \App\Models\BarangMasuk::JENIS_REGULER", $content);
    $content = str_replace("'jenis_transaksi' => 'Return'", "'jenis_transaksi' => \App\Models\BarangMasuk::JENIS_RETURN", $content);
    
    $content = str_replace("'jenis' => 'mutasi'", "'jenis' => \App\Models\BarangKeluar::JENIS_MUTASI", $content);
    $content = str_replace("'jenis' => 'reguler'", "'jenis' => \App\Models\BarangKeluar::JENIS_REGULER", $content);

    // specific to barangkeluarcontroller
    $content = str_replace("\$status = \$request->jenis === 'mutasi' ? 'pending' : 'completed'", "\$status = \$request->jenis === 'mutasi' ? \App\Models\BarangKeluar::STATUS_PENDING : \App\Models\BarangKeluar::STATUS_COMPLETED", $content);

    file_put_contents($file, $content);
}

$views = [
    __DIR__ . '/resources/views/barang_masuk/index.blade.php',
    __DIR__ . '/resources/views/barang_masuk/show.blade.php',
    __DIR__ . '/resources/views/barang_keluar/index.blade.php',
    __DIR__ . '/resources/views/barang_keluar/show.blade.php',
    __DIR__ . '/resources/views/laporan/rekap.blade.php',
    __DIR__ . '/resources/views/dashboard.blade.php',
];

foreach ($views as $view) {
    if (!file_exists($view)) continue;
    $content = file_get_contents($view);
    
    // In Blade, we check integer directly to be safe and clean, or we can use the accessors if we want string logic.
    // It's cleaner to just check int.
    $content = str_replace("\$tx->status === 'pending'", "\$tx->status == 1", $content);
    $content = str_replace("\$tx->status === 'completed'", "\$tx->status == 2", $content);
    $content = str_replace("\$tx->status === 'approved'", "\$tx->status == 2", $content);
    $content = str_replace("\$tx->status === 'rejected'", "\$tx->status == 3", $content);
    
    $content = str_replace("\$tx->jenis_transaksi === 'Mutasi Ditolak'", "\$tx->jenis_transaksi == 3", $content);
    
    $content = str_replace("\$row->status === 'pending'", "\$row->status == 1", $content);
    
    $content = str_replace("\$jenisTrans === 'Mutasi'", "\$jenisTrans == 2", $content);
    $content = str_replace("\$jenisTrans === 'Stock Opname'", "\$jenisTrans == 4", $content);
    $content = str_replace("\$jenisTrans === 'Retur' || \$jenisTrans === 'Return'", "\$jenisTrans == 3", $content);
    
    $content = str_replace("\$jenisTrans === 'mutasi'", "\$jenisTrans == 2", $content);
    $content = str_replace("\$jenisTrans === 'stock_opname'", "\$jenisTrans == 4", $content);
    $content = str_replace("\$jenisTrans === 'retur'", "\$jenisTrans == 3", $content);

    // Dashboard might have some specific queries
    $content = str_replace("where('status', 'pending')", "where('status', 1)", $content);

    file_put_contents($view, $content);
}
echo "Refactoring completed successfully!\n";
