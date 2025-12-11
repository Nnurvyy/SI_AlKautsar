<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKategoriKeuanganTable extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_keuangan', function (Blueprint $table) {
            $table->uuid('id_kategori_keuangan')->primary();
            $table->string('nama_kategori_keuangan', 100);

            
            $table->enum('tipe', ['pengeluaran', 'pemasukan']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_keuangan');
    }
}
