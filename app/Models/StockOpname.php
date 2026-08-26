<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_opname',
        'tanggal_opname',
        'gudang_kode',
        'status',
        'user_id',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_opname' => 'date'
    ];

    /**
     * Get the target warehouse.
     */
    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_kode', 'kode_gudang');
    }

    /**
     * Get the detailed audited item rows.
     */
    public function details()
    {
        return $this->hasMany(DetailStockOpname::class, 'stock_opname_id');
    }

    /**
     * Get the user who recorded this opname document.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
