<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tabungan_hewan_qurban', function (Blueprint $table) {
            $table->uuid('id_tabungan_hewan_qurban')->primary();
            $table->unsignedBigInteger('id_jamaah'); 
            
            // Status default adalah menunggu
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'selesai'])->default('menunggu');
            $table->date('tanggal_pembuatan');
            
            $table->enum('saving_type', ['bebas', 'cicilan'])->default('cicilan');
            $table->integer('duration_months')->nullable(); // Jika cicilan, berapa bulan
            
            $table->bigInteger('total_tabungan')->default(0); // Uang yang sudah disetor
            $table->bigInteger('total_harga_hewan_qurban')->default(0); // Target total harga (sum dari detail)
            
            $table->timestamps();

            // Pastikan tabel jamaah sudah ada, jika belum, hapus foreign key ini dulu
            $table->foreign('id_jamaah')->references('id')->on('jamaah')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tabungan_hewan_qurban');
    }
};