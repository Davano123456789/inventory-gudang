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
            $table->dropColumn(['debitur', 'penerima', 'alamat_kirim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->string('debitur', 100)->nullable();
            $table->string('penerima', 200);
            $table->text('alamat_kirim');
        });
    }
};
