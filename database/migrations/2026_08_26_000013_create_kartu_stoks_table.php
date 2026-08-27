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
        Schema::create('kartu_stoks', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal');
            $table->string('kode_gudang', 50);
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->decimal('saldo_awal', 15, 2)->default(0.00);
            $table->decimal('masuk', 15, 2)->default(0.00);
            $table->decimal('keluar', 15, 2)->default(0.00);
            $table->decimal('saldo_akhir', 15, 2);
            $table->foreignId('barang_masuk_id')->nullable()->constrained('barang_masuks')->onDelete('cascade');
            $table->foreignId('barang_keluar_id')->nullable()->constrained('barang_keluars')->onDelete('cascade');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            // Set up relationship to gudangs
            $table->foreign('kode_gudang')->references('kode_gudang')->on('gudangs')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_stoks');
    }
};
