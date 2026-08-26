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
        Schema::create('stok_gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_gudang', 50);
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->decimal('stok_sekarang', 15, 2)->default(0.00);
            $table->timestamps();

            $table->unique(['kode_gudang', 'barang_id']);
            $table->foreign('kode_gudang')->references('kode_gudang')->on('gudangs')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_gudangs');
    }
};
