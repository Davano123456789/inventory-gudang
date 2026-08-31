<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kode_gudang',
        'must_change_password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the assigned warehouse.
     */
    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'kode_gudang', 'kode_gudang');
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is Kepala Gudang.
     */
    public function isKepalaGudang(): bool
    {
        return $this->role === 'kepala_gudang';
    }

    /**
     * Check if user is Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is Staff Gudang.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff_gudang';
    }

    /**
     * Get active warehouse code.
     * If user is a Kepala Gudang or Staff, always returns their assigned warehouse.
     * If user is Super Admin, returns selected session warehouse or 'all'.
     */
    public function getActiveGudangCode(): string
    {
        if ($this->isKepalaGudang() || $this->isStaff()) {
            return $this->kode_gudang ?? '';
        }
        
        // Super Admin dan Admin bisa memilih gudang (global)
        $sessionCode = session('active_gudang_kode');
        if (!$sessionCode || $sessionCode === 'all') {
            $firstGudang = \App\Models\Gudang::orderBy('nama_gudang', 'asc')->first();
            $defaultCode = $firstGudang ? $firstGudang->kode_gudang : '';
            session(['active_gudang_kode' => $defaultCode]);
            return $defaultCode;
        }

        return $sessionCode;
    }
}
