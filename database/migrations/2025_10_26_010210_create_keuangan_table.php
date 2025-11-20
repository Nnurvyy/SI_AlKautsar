<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('keuangan', function (Blueprint $table) {
            $table->uuid('id_keuangan')->primary();
            $table->enum('tipe', ['pemasukan', 'pengeluaran']);
            $table->date('tanggal');
            $table->bigInteger('nominal');
            $table->uuid('id_kategori_keuangan');
            $table->string('deskripsi', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_kategori_keuangan')->references('id_kategori_keuangan')->on('kategori_keuangan')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('keuangan');
    }
};


