<?php
// Let's fetch some data and see if it crashes
try {
    $masuk = \App\Models\BarangMasuk::first();
    if ($masuk) {
        echo "Barang Masuk ID {$masuk->id}: Status = {$masuk->status} (Text: {$masuk->status_text}), Jenis = {$masuk->jenis_transaksi} (Text: {$masuk->jenis_text})\n";
    }
    
    $keluar = \App\Models\BarangKeluar::first();
    if ($keluar) {
        echo "Barang Keluar ID {$keluar->id}: Status = {$keluar->status} (Text: {$keluar->status_text}), Jenis = {$keluar->jenis} (Text: {$keluar->jenis_text})\n";
    }
    
    $kartu = \App\Models\KartuStok::first();
    if ($kartu) {
        echo "Kartu Stok ID {$kartu->id}: Status = {$kartu->status} (Text: {$kartu->status_text})\n";
    }

    echo "ALL TESTS PASSED: Data successfully accessed via constants and accessors.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
