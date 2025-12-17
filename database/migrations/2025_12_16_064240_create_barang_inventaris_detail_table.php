<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barang_inventaris_detail', function (Blueprint $table) {
            // ID unik untuk detail barang
            $table->uuid('id_detail_barang')->primary();

            // Kunci asing yang menghubungkan ke tabel master barang_inventaris
            $table->uuid('id_barang');
            $table->foreign('id_barang')->references('id_barang')->on('barang_inventaris')->onDelete('cascade');

            $table->string('kode_barang', 100)->unique();
            $table->string('kondisi', 50)->default('Baik');
            $table->string('status', 50)->default('Tersedia');
            $table->text('deskripsi')->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->date('tanggal_masuk')->default(DB::raw('CURRENT_DATE'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_inventaris_detail');
    }
};