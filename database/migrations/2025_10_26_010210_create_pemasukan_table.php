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
            $table->uuid('id_divisi');
            $table->uuid('id_siswa')->nullable();
            $table->uuid('id_kategori');

            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'lainnya'])->default('tunai');
            $table->bigInteger('nominal');
            $table->text('deskripsi')->nullable();
            $table->date('tanggal_transaksi');
            $table->string('nomor_kwitansi', 9)->nullable();

            $table->timestamps();

            // Relasi FK
            $table->foreign('id_divisi')
                  ->references('id_divisi')
                  ->on('divisi')
                  ->onDelete('cascade');

            $table->foreign('id_siswa')
                  ->references('id_students')
                  ->on('students')
                  ->onDelete('set null');

            $table->foreign('id_kategori')
                  ->references('id_pemasukan_kategori')
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

