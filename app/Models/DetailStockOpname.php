<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailStockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_opname_id',
        'barang_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan'
    ];

    protected $casts = [
        'stok_sistem' => 'decimal:2',
        'stok_fisik' => 'decimal:2',
        'selisih' => 'decimal:2'
    ];

    /**
     * Get the parent opname document.
     */
    public function parent()
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    /**
     * Get the adjusted item.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
