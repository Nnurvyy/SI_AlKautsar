<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pemasukan_tabungan_qurban', function (Blueprint $table) {
            $table->uuid('id_pemasukan_tabungan_qurban')->primary();
            
            
            $table->uuid('id_tabungan_hewan_qurban');
            
            
            $table->string('order_id')->nullable()->unique(); 
            $table->string('tripay_reference')->nullable();   
            $table->string('checkout_url')->nullable();       
            
            $table->date('tanggal');
            $table->bigInteger('nominal');
            
            
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'tripay'])->default('tunai');
            $table->string('status')->default('pending'); 
            
            $table->timestamps();

            
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