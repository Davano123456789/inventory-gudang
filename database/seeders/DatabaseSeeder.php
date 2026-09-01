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
            'nama_gudang' => 'Gudang Cabang Surabaya',
            'alamat' => 'Surabaya'
        ]);

        Gudang::firstOrCreate(['kode_gudang' => '000003'], [
            'nama_gudang' => 'Gudang Utama Jakarta',
            'alamat' => 'Jakarta'
        ]);

        // 2. Seed Users
        User::updateOrCreate(['email' => 'admin@test.com'], [
            'name' => 'Admin Utama',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'kode_gudang' => null,
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['email' => 'admin_biasa@test.com'], [
            'name' => 'Admin Operasional',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'kode_gudang' => null,
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['email' => 'surabaya@test.com'], [
            'name' => 'Budi (Gudang Surabaya)',
            'password' => bcrypt('password'),
            'role' => 'kepala_gudang',
            'kode_gudang' => '300003',
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['email' => 'jakarta@test.com'], [
            'name' => 'Rian (Kepala Gudang Jakarta)',
            'password' => bcrypt('password'),
            'role' => 'kepala_gudang',
            'kode_gudang' => '000003',
            'must_change_password' => false
        ]);

        User::updateOrCreate(['email' => 'staff_sby@test.com'], [
            'name' => 'Siti (Staff Surabaya)',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'kode_gudang' => '300003',
            'must_change_password' => false,
        ]);

        User::updateOrCreate(['email' => 'staff_jkt@test.com'], [
            'name' => 'Joko (Staff Jakarta)',
            'password' => bcrypt('password'),
            'role' => 'staff_gudang',
            'kode_gudang' => '000003',
            'must_change_password' => false
        ]);

        // Seed default application settings
        \App\Models\Setting::updateOrCreate(['key' => 'min_stok_alert'], [
            'value' => '5',
            'keterangan' => 'Batas minimum stok untuk peringatan stok rendah di dashboard'
        ]);
    }
}
