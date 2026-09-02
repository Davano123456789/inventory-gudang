<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    // Constants for Status
    public const STATUS_PENDING = 1;
    public const STATUS_COMPLETED = 2; // Also used for Approved
    public const STATUS_REJECTED = 3;

    // Constants for Jenis Transaksi
    public const JENIS_REGULER = 1;
    public const JENIS_MUTASI = 2;
    public const JENIS_RETUR = 3;
    public const JENIS_STOCK_OPNAME = 4;

    protected $fillable = [
        'no_surat_jalan',
        'tanggal_keluar',
        'tanggal_surat_jalan',
        'jenis',
        'gudang_asal_kode',
        'gudang_tujuan_kode',
        'status',
        'user_id',
        'approved_by',
        'approved_at',
        'catatan'
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
        'tanggal_surat_jalan' => 'date',
        'approved_at' => 'datetime'
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
        return match($this->jenis) {
            self::JENIS_REGULER => 'reguler',
            self::JENIS_MUTASI => 'mutasi',
            self::JENIS_RETUR => 'retur',
            self::JENIS_STOCK_OPNAME => 'stock_opname',
            default => 'reguler',
        };
    }

    /**
     * Get the user who registered this transaction.
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
}
