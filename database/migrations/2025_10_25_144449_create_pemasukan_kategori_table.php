<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pemasukan_kategori', function (Blueprint $table) {
            // Menggunakan UUID sebagai primary key
            $table->uuid('id_pemasukan_kategori')->primary(); 
            
            $table->string('nama_pemasukan_kategori', 100);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasukan_kategori');
    }
};