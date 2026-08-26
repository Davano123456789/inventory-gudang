<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_surat_jalan',
        'tanggal_keluar',
        'tanggal_surat_jalan',
        'jenis',
        'gudang_asal_kode',
        'gudang_tujuan_kode',
        'status',
        'user_id',
        'catatan'
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
        'tanggal_surat_jalan' => 'date'
    ];

    /**
     * Get the source warehouse.
     */
    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_asal_kode', 'kode_gudang');
    }

    /**
     * Get the destination warehouse (for mutasi).
     */
    public function gudangTujuan()
    {
        return $this->belongsTo(Gudang::class, 'gudang_tujuan_kode', 'kode_gudang');
    }

    /**
     * Get the details of this transaction.
     */
    public function details()
    {
        return $this->hasMany(DetailBarangKeluar::class, 'barang_keluar_id');
    }

    /**
     * Get the user who registered this transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
