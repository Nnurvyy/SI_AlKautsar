<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::create('barang_inventaris', function (Blueprint $table) {
            $table->uuid('id_barang')->primary();
            $table->string('nama_barang', 100);
            $table->string('satuan', 20)->nullable();
            $table->string('kondisi', 50)->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

        public function down(): void
    {
        Schema::dropIfExists('barang_inventaris');
    }
};