<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('nama_size', 50);
            $table->string('kategori', 50)->nullable()->default('General');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Insert initial default size: ID 1 = ALL / ONE SIZE
        DB::table('sizes')->insert([
            'id' => 1,
            'nama_size' => 'ALL / ONE SIZE',
            'kategori' => 'General',
            'urutan' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};
