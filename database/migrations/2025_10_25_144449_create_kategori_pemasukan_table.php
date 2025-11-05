<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKategoriPemasukanTable extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_pemasukan', function (Blueprint $table) {
            // Menggunakan UUID sebagai primary key
            $table->uuid('id_kategori_pemasukan')->primary();
            $table->string('nama_kategori_pemasukan', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_pemasukan');
    }
}
