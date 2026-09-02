<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan_id',
        'size_id',
        'created_by_user_id',
        'gudang_pendaftar_kode',
        'stok_global'
    ];

    /**
     * Get the unit (satuan) associated with the item.
     */
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    /**
     * Get the size associated with the item.
     */
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    /**
     * Get the stock records associated with the item.
     */
    public function stokGudangs()
    {
        return $this->hasMany(StokGudang::class, 'barang_id');
    }

    /**
     * Get the user who created the item.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get the warehouse where the item was first registered.
     */
    public function gudangPendaftar()
    {
        return $this->belongsTo(Gudang::class, 'gudang_pendaftar_kode', 'kode_gudang');
    }
}
