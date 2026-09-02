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
        // 1. Add size_id to barangs table
        Schema::table('barangs', function (Blueprint $table) {
            $table->unsignedBigInteger('size_id')->default(1)->after('satuan_id');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });

        // 2. Add size_id to stok_gudangs table
        Schema::table('stok_gudangs', function (Blueprint $table) {
            $table->unsignedBigInteger('size_id')->default(1)->after('barang_id');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });

        // 3. Add size_id to detail_barang_masuks table
        Schema::table('detail_barang_masuks', function (Blueprint $table) {
            $table->unsignedBigInteger('size_id')->default(1)->after('barang_id');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });

        // 4. Add size_id to detail_barang_keluars table
        Schema::table('detail_barang_keluars', function (Blueprint $table) {
            $table->unsignedBigInteger('size_id')->default(1)->after('barang_id');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });

        // 5. Add size_id to kartu_stoks table
        Schema::table('kartu_stoks', function (Blueprint $table) {
            $table->unsignedBigInteger('size_id')->default(1)->after('barang_id');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kartu_stoks', function (Blueprint $table) {
            $table->dropForeign(['size_id']);
            $table->dropColumn('size_id');
        });

        Schema::table('detail_barang_keluars', function (Blueprint $table) {
            $table->dropForeign(['size_id']);
            $table->dropColumn('size_id');
        });

        Schema::table('detail_barang_masuks', function (Blueprint $table) {
            $table->dropForeign(['size_id']);
            $table->dropColumn('size_id');
        });

        Schema::table('stok_gudangs', function (Blueprint $table) {
            $table->dropForeign(['size_id']);
            $table->dropColumn('size_id');
        });

        Schema::table('barangs', function (Blueprint $table) {
            $table->dropForeign(['size_id']);
            $table->dropColumn('size_id');
        });
    }
};
