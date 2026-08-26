<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokGudang extends Model
{
    protected $table = 'stok_gudangs';

    protected $fillable = [
        'kode_gudang',
        'barang_id',
        'stok_sekarang'
    ];

    /**
     * Get the warehouse associated with this stock.
     */
    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }

    /**
     * Get the item associated with this stock.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
