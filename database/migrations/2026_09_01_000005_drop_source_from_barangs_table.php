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
            // Drop foreign key on gudang_pendaftar_kode if exists so it can store 'excel' or warehouse code
            try {
                $table->dropForeign(['gudang_pendaftar_kode']);
            } catch (\Exception $e) {}

            if (Schema::hasColumn('barangs', 'source')) {
                $table->dropColumn('source');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('source')->nullable()->after('created_by_user_id');
        });
    }
};
