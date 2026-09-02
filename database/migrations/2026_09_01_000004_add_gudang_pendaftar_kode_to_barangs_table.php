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
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('gudang_pendaftar_kode')->nullable()->after('source');
            $table->foreign('gudang_pendaftar_kode')->references('kode_gudang')->on('gudangs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropForeign(['gudang_pendaftar_kode']);
            $table->dropColumn('gudang_pendaftar_kode');
        });
    }
};
