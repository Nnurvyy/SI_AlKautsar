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
        Schema::table('jamaah', function (Blueprint $table) {
            // Kita ubah kolom no_hp agar boleh kosong (nullable)
            $table->string('no_hp')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah', function (Blueprint $table) {
            // Kembalikan ke tidak boleh kosong (jika di-rollback)
            $table->string('no_hp')->nullable(false)->change();
        });
    }
};