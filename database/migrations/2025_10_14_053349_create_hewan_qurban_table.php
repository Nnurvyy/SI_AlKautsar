<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hewan_qurban', function (Blueprint $table) {
            $table->uuid('id_hewan_qurban')->primary();
            // Enum nama hewan dan kategori dipisah agar fleksibel
            $table->enum('nama_hewan', ['kambing', 'kerbau', 'domba', 'sapi', 'unta']);
            $table->enum('kategori_hewan', ['premium', 'reguler', 'basic']);
            $table->bigInteger('harga_hewan')->default(0); // Gunakan BigInt untuk harga
            $table->boolean('is_active')->default(true); // Soft delete simpel untuk menyembunyikan hewan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hewan_qurban');
    }
};