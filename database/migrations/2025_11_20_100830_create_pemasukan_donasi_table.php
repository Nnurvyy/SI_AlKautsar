<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::create('pemasukan_donasi', function (Blueprint $table) {
            $table->uuid('id_pemasukan_donasi')->primary();

            
            $table->uuid('id_donasi');
            $table->string('order_id')->nullable()->unique();
            $table->foreign('id_donasi')
                  ->references('id_donasi')
                  ->on('donasi')
                  ->onDelete('cascade');
            
            $table->unsignedBigInteger('id_jamaah')->nullable(); 

            $table->date('tanggal');
            $table->string('nama_donatur');
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'whatsapp'])->default('tunai');
            $table->bigInteger('nominal');
            $table->string('status')->default('pending');
            $table->string('tripay_reference')->nullable(); 
            $table->string('checkout_url')->nullable();
            $table->text('pesan')->nullable();

            $table->timestamps();

             $table->foreign('id_jamaah')->references('id')->on('jamaah')->onDelete('cascade');
        });
    }

        public function down(): void
    {
        Schema::dropIfExists('pemasukan_donasi');
    }
};
