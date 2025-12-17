<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dulu: Jika tabel 'donasi' BELUM ada, baru buat
        if (!Schema::hasTable('donasi')) {
            
            Schema::create('donasi', function (Blueprint $table) {
                $table->uuid('id_donasi')->primary();
                $table->string('nama_donasi');
                $table->string('foto_donasi')->nullable();
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai')->nullable();
                $table->bigInteger('target_dana');
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
            
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('donasi');
    }
};