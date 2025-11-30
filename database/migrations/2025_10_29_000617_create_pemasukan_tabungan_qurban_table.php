<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pemasukan_tabungan_qurban', function (Blueprint $table) {
            $table->uuid('id_pemasukan_tabungan_qurban')->primary();
            
            // Relasi ke tabungan induk
            $table->uuid('id_tabungan_hewan_qurban');
            
            // Kolom Tripay & Identifikasi Transaksi
            $table->string('order_id')->nullable()->unique(); // Kode unik transaksi (Misal: TRQ-123...)
            $table->string('tripay_reference')->nullable();   // Ref dari Tripay
            $table->string('checkout_url')->nullable();       // Link bayar
            
            $table->date('tanggal');
            $table->bigInteger('nominal');
            
            // Metode Pembayaran & Status
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'tripay'])->default('tunai');
            $table->string('status')->default('pending'); // pending, success, failed, expire
            
            $table->timestamps();

            // Foreign key
            $table->foreign('id_tabungan_hewan_qurban')
                  ->references('id_tabungan_hewan_qurban')
                  ->on('tabungan_hewan_qurban')
                  ->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('pemasukan_tabungan_qurban');
    }
};