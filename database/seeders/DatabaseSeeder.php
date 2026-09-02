<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Gudang;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Gudang first to prevent foreign key errors
        Gudang::firstOrCreate(['kode_gudang' => '300003'], [
            'nama_gudang' => 'Gudang Surabaya',
            'alamat' => 'Surabaya'
        ]);

        Gudang::firstOrCreate(['kode_gudang' => '000003'], [
            'nama_gudang' => 'Gudang Sidoarjo',
            'alamat' => 'Sidoarjo'
        ]);

        // 2. Seed Users
        User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['username' => 'admin_biasa'], [
            'name' => 'Admin Gudang',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['username' => 'kepala_sby'], [
            'name' => 'Kepala Gudang SBY',
            'password' => bcrypt('password'),
            'role' => 'kepala_gudang',
            'kode_gudang' => '300003',
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['username' => 'kepala_jkt'], [
            'name' => 'Kepala Gudang JKT',
            'password' => bcrypt('password'),
            'role' => 'kepala_gudang',
            'kode_gudang' => '000003',
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['username' => 'staff_sby'], [
            'name' => 'Staff SBY 1',
            'password' => bcrypt('password'),
            'role' => 'staff_gudang',
            'kode_gudang' => '300003',
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['username' => 'staff_jkt'], [
            'name' => 'Joko (Staff Jakarta)',
            'password' => bcrypt('password'),
            'role' => 'staff_gudang',
            'kode_gudang' => '000003',
            'must_change_password' => false,
        ]);

        // Seed default application settings
        \App\Models\Setting::updateOrCreate(['key' => 'min_stok_alert'], [
            'value' => '5',
            'keterangan' => 'Batas minimum stok untuk peringatan stok rendah di dashboard'
        ]);
    }
}
