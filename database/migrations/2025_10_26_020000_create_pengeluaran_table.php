<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->uuid('id_pengeluaran')->primary();
            $table->date('tanggal');
            $table->bigInteger('nominal');
            $table->uuid('id_kategori_pengeluaran');
            $table->string('deskripsi', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_kategori_pengeluaran')->references('id_kategori_pengeluaran')->on('kategori_pengeluaran')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('pengeluaran');
    }
};

