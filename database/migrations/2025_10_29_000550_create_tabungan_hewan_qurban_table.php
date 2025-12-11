<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tabungan_hewan_qurban', function (Blueprint $table) {
            $table->uuid('id_tabungan_hewan_qurban')->primary();
            $table->unsignedBigInteger('id_jamaah'); 
            
            
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'selesai'])->default('menunggu');
            $table->date('tanggal_pembuatan');
            
            $table->enum('saving_type', ['bebas', 'cicilan'])->default('cicilan');
            $table->integer('duration_months')->nullable(); 
            
            $table->bigInteger('total_tabungan')->default(0); 
            $table->bigInteger('total_harga_hewan_qurban')->default(0); 
            
            $table->timestamps();

            
            $table->foreign('id_jamaah')->references('id')->on('jamaah')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tabungan_hewan_qurban');
    }
};