<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->string('jenis', 50)->default('reguler')->after('sales_tipe');
            $table->string('gudang_tujuan_kode', 20)->nullable()->after('gudang_asal_kode');
            $table->string('status', 20)->default('completed')->after('gudang_tujuan_kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->dropColumn(['jenis', 'gudang_tujuan_kode', 'status']);
        });
    }
};
