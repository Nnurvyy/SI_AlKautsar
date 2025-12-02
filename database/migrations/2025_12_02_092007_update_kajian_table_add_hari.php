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
        Schema::table('kajian', function (Blueprint $table) {
            // 1. Tambah kolom 'hari' (nullable karena event besar tidak butuh hari)
            // Kita taruh setelah 'tema_kajian' biar rapi
            $table->string('hari', 20)->nullable()->after('tema_kajian');

            // 2. Ubah kolom 'tanggal_kajian' jadi nullable 
            // (karena kajian rutin tidak butuh tanggal spesifik)
            $table->date('tanggal_kajian')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kajian', function (Blueprint $table) {
            // Hapus kolom hari jika rollback
            $table->dropColumn('hari');

            // Kembalikan tanggal jadi wajib diisi (NOT NULL)
            // PERHATIAN: Ini bisa error jika saat rollback ada data yang tanggalnya NULL
            $table->date('tanggal_kajian')->nullable(false)->change();
        });
    }
};