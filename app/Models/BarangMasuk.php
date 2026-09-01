<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuks';

    protected $fillable = [
        'no_surat_jalan',
        'tanggal_masuk',
        'tanggal_surat_jalan',
        'jenis_transaksi',
        'status',
        'gudang_asal_kode',
        'pengirim',
        'gudang_tujuan_kode',
        'user_id',
        'catatan'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_surat_jalan' => 'date'
    ];

    /**
     * Get the source warehouse (for mutasi).
     */
    public function gudangAsal()
    {
        return $this->belongsTo(Gudang::class, 'gudang_asal_kode', 'kode_gudang');
    }

    /**
     * Get the destination warehouse.
     */
    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_tujuan_kode', 'kode_gudang');
    }

    /**
     * Get the user who recorded the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the detail items for this transaction.
     */
    public function details()
    {
        return $this->hasMany(DetailBarangMasuk::class, 'barang_masuk_id');
    }
}
