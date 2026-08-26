<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailBarangMasuk extends Model
{
    protected $table = 'detail_barang_masuks';

    protected $fillable = [
        'barang_masuk_id',
        'barang_id',
        'qty_box',
        'qty_pcs',
        'qty_total'
    ];

    /**
     * Get the item associated with this detail.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Get the incoming transaction header.
     */
    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }
}
