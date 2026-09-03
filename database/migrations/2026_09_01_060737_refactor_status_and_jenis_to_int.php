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
        // 1. BARANG MASUKS
        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->tinyInteger('status_int')->default(2)->after('status')->comment('1 = Pending; 2 = Completed/Approved; 3 = Rejected');
            $table->tinyInteger('jenis_transaksi_int')->default(1)->after('jenis_transaksi')->comment('1 = Reguler; 2 = Mutasi; 3 = Retur; 4 = Stock Opname');
        });

        DB::statement("UPDATE barang_masuks SET status_int = CASE 
            WHEN status = 'pending' THEN 1 
            WHEN status = 'completed' OR status = 'approved' THEN 2 
            WHEN status = 'rejected' THEN 3 
            ELSE 2 END");
        
        DB::statement("UPDATE barang_masuks SET jenis_transaksi_int = CASE 
            WHEN jenis_transaksi = 'Biasa' OR jenis_transaksi = 'Reguler' THEN 1 
            WHEN jenis_transaksi = 'Mutasi' THEN 2 
            WHEN jenis_transaksi = 'Return' OR jenis_transaksi = 'Retur' THEN 3 
            WHEN jenis_transaksi = 'Stock Opname' THEN 4 
            ELSE 1 END");

        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('jenis_transaksi');
        });

        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->renameColumn('status_int', 'status');
            $table->renameColumn('jenis_transaksi_int', 'jenis_transaksi');
        });

        // 2. BARANG KELUARS
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->tinyInteger('status_int')->default(2)->after('status')->comment('1 = Pending; 2 = Completed/Approved; 3 = Rejected');
            $table->tinyInteger('jenis_int')->default(1)->after('jenis')->comment('1 = Reguler; 2 = Mutasi; 3 = Retur; 4 = Stock Opname');
        });

        DB::statement("UPDATE barang_keluars SET status_int = CASE 
            WHEN status = 'pending' THEN 1 
            WHEN status = 'completed' OR status = 'approved' THEN 2 
            WHEN status = 'rejected' THEN 3 
            ELSE 2 END");
        
        DB::statement("UPDATE barang_keluars SET jenis_int = CASE 
            WHEN jenis = 'reguler' OR jenis = 'biasa' THEN 1 
            WHEN jenis = 'mutasi' THEN 2 
            WHEN jenis = 'retur' THEN 3 
            WHEN jenis = 'stock_opname' THEN 4 
            ELSE 1 END");

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('jenis');
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->renameColumn('status_int', 'status');
            $table->renameColumn('jenis_int', 'jenis');
        });

        // 3. KARTU STOKS
        Schema::table('kartu_stoks', function (Blueprint $table) {
            $table->tinyInteger('status_int')->default(2)->after('status')->comment('1 = Pending; 2 = Completed/Approved; 3 = Rejected');
        });

        DB::statement("UPDATE kartu_stoks SET status_int = CASE 
            WHEN status = 'pending' THEN 1 
            WHEN status = 'completed' THEN 2 
            ELSE 2 END");

        Schema::table('kartu_stoks', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('kartu_stoks', function (Blueprint $table) {
            $table->renameColumn('status_int', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To reverse, we would change it back to string. But dropping the columns is complex here.
    }
};
