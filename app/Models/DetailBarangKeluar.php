<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBarangKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_keluar_id',
        'barang_id',
        'size_id',
        'qty'
    ];

    /**
     * Get the parent outgoing transaction.
     */
    public function parent()
    {
        return $this->belongsTo(BarangKeluar::class, 'barang_keluar_id');
    }

    /**
     * Get the item.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Get the size.
     */
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
}
