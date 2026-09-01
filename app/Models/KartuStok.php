<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KartuStok extends Model
{
    use HasFactory;

    // Constants for Status
    public const STATUS_PENDING = 1;
    public const STATUS_COMPLETED = 2;

    protected $table = 'kartu_stoks';

    protected $fillable = [
        'tanggal',
        'kode_gudang',
        'barang_id',
        'saldo_awal',
        'masuk',
        'keluar',
        'saldo_akhir',
        'barang_masuk_id',
        'barang_keluar_id',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'saldo_awal' => 'decimal:2',
        'masuk' => 'decimal:2',
        'keluar' => 'decimal:2',
        'saldo_akhir' => 'decimal:2'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'barang_masuk_id');
    }

    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'barang_keluar_id');
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'pending',
            self::STATUS_COMPLETED => 'completed',
            default => 'completed',
        };
    }
}
