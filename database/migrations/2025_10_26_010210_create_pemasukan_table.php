<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pemasukan', function (Blueprint $table) {
            $table->uuid('id_pemasukan')->primary();
            $table->date('tanggal');
            $table->bigInteger('nominal');
            $table->uuid('id_kategori_pemasukan');
            $table->string('deskripsi', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_kategori_pemasukan')->references('id_kategori_pemasukan')->on('kategori_pemasukan')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('pemasukan');
    }
};


