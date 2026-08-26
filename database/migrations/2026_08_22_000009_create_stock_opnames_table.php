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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('no_opname', 100)->unique();
            $table->date('tanggal_opname');
            $table->string('gudang_kode', 50);
            $table->enum('status', ['Draft', 'Selesai'])->default('Draft');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('gudang_kode')->references('kode_gudang')->on('gudangs')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
