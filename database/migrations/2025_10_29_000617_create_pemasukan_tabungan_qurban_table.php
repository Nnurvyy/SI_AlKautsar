<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pemasukan_tabungan_qurban', function (Blueprint $table) {
            $table->uuid('id_pemasukan_tabungan_qurban')->primary();
            $table->uuid('id_tabungan_hewan_qurban');
            $table->uuid('id_pengguna');
            $table->date('tanggal');
            $table->bigInteger('nominal');
            $table->timestamps();

            // foreign key ke pengguna
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('pemasukan_tabungan_qurban');
    }
};