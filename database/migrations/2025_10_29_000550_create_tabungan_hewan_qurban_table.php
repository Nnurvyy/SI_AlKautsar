<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tabungan_hewan_qurban', function (Blueprint $table) {
            $table->uuid('id_tabungan_hewan_qurban')->primary();
            $table->string('nama_hewan', 100);
            $table->integer('total_hewan')->default(0);
            $table->bigInteger('total_tabungan')->default(0);
            $table->uuid('id_pengguna'); // UUID sesuai users
            $table->bigInteger('total_harga_hewan_qurban')->default(0);
            $table->timestamps();

            // foreign key ke pengguna
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tabungan_hewan_qurban');
    }
};
