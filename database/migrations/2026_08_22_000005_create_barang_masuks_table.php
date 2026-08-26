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
        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat_jalan', 100)->unique();
            $table->date('tanggal_masuk');
            $table->date('tanggal_surat_jalan');
            $table->string('jenis_transaksi', 20)->default('Biasa');
            $table->string('gudang_asal_kode', 50)->nullable();
            $table->string('pengirim', 150)->nullable();
            $table->string('gudang_tujuan_kode', 50);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('gudang_tujuan_kode')->references('kode_gudang')->on('gudangs')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('gudang_asal_kode')->references('kode_gudang')->on('gudangs')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuks');
    }
};
