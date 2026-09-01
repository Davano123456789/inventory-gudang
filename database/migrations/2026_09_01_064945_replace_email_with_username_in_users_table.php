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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name')->nullable();
        });

        // We migrate existing users by taking lowercase name with underscores. If there are no users yet, this is harmless.
        DB::statement("UPDATE users SET username = LOWER(REPLACE(name, ' ', '_'))");

        Schema::table('users', function (Blueprint $table) {
            // After population, make it not null
            $table->string('username')->nullable(false)->change();
            
            $table->dropColumn('email');
            $table->dropColumn('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique()->after('username')->nullable();
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });

        DB::statement("UPDATE users SET email = CONCAT(username, '@example.com')");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
