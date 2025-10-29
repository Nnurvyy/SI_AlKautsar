<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk tabel pemasukan.
     */
    public function up(): void
    {
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->uuid('id_pemasukan')->primary();
            $table->date('tanggal');
            $table->bigInteger('nominal');
            $table->uuid('id_kategori_pemasukan');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // Relasi ke tabel kategori_pemasukan
            $table->foreign('id_kategori_pemasukan')
                  ->references('id_kategori_pemasukan')
                  ->on('pemasukan_kategori')
                  ->onDelete('cascade');
        });
    }

    /**
     * Rollback tabel pemasukan.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasukan');
    }
};
