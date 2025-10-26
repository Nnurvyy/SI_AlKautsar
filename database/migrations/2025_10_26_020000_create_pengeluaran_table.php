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
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->uuid('id_pengeluaran')->primary();

            // Foreign keys
            $table->uuid('id_divisi')->nullable();
            $table->uuid('id_kategori')->nullable();

            // Kolom utama
            $table->bigInteger('nominal');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_transaksi');
            $table->string('nomor_kwitansi', 9)->nullable();
            $table->string('penerima', 100)->nullable();

            $table->timestamps();

            // Relasi
            $table->foreign('id_divisi')->references('id_divisi')->on('divisi')->onDelete('set null');
            $table->foreign('id_kategori')->references('id_pengeluaran_kategori')->on('pengeluaran_kategori')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
