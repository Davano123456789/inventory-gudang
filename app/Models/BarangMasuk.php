<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarangMasuk extends Model
{
    use HasFactory;

    // Constants for Status
    public const STATUS_PENDING = 1;
    public const STATUS_COMPLETED = 2; // Also used for Approved
    public const STATUS_REJECTED = 3;

    // Constants for Jenis Transaksi
    public const JENIS_REGULER = 1;
    public const JENIS_MUTASI = 2;
    public const JENIS_RETURN = 3;
    public const JENIS_STOCK_OPNAME = 4;

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
        'approved_by',
        'approved_at',
        'catatan'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_surat_jalan' => 'date',
        'approved_at' => 'datetime'
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
     * Get the user who approved or rejected this transaction.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the detail items for this transaction.
     */
    public function details()
    {
        return $this->hasMany(DetailBarangMasuk::class, 'barang_masuk_id');
    }



    public function getStatusTextAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'pending',
            self::STATUS_COMPLETED => 'completed',
            self::STATUS_REJECTED => 'rejected',
            default => 'unknown',
        };
    }

    public function getJenisTextAttribute()
    {
        return match($this->jenis_transaksi) {
            self::JENIS_REGULER => 'Reguler',
            self::JENIS_MUTASI => 'Mutasi',
            self::JENIS_RETURN => 'Return',
            self::JENIS_STOCK_OPNAME => 'Stock Opname',
            default => 'Reguler',
        };
    }
}
