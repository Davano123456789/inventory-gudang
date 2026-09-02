<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokGudang extends Model
{
    protected $table = 'stok_gudangs';

    protected $fillable = [
        'kode_gudang',
        'barang_id',
        'size_id',
        'stok_sekarang'
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        $syncStokGlobal = function ($stokGudang) {
            $barang = $stokGudang->barang;
            if ($barang) {
                $barang->stok_global = StokGudang::where('barang_id', $barang->id)->sum('stok_sekarang');
                $barang->save();
            }
        };

        static::saved($syncStokGlobal);
        static::deleted($syncStokGlobal);
    }

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

    /**
     * Get the size associated with this stock.
     */
    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
}
