<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasukan_kategori', function (Blueprint $table) {
            // Primary key sesuai format di gambar
            $table->uuid('id_kategori_pemasukan')->primary(); 
            $table->string('nama_kategori_pemasukan', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukan_kategori');
    }
};
