<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->uuid('id_pemasukan')->primary();
            $table->date('tanggal');
            $table->bigInteger('nominal');
            $table->unsignedBigInteger('id_kategori'); // tipe harus cocok dengan kolom id di kategori_pemasukan
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            // Relasi ke tabel kategori_pemasukan
            $table->foreign('id_kategori')
                  ->references('id')
                  ->on('kategori_pemasukan')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukan');
    }
};
