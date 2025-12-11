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
            
            $table->enum('nama_hewan', ['kambing', 'kerbau', 'domba', 'sapi', 'unta']);
            $table->enum('kategori_hewan', ['premium', 'reguler', 'basic']);
            $table->bigInteger('harga_hewan')->default(0); 
            $table->boolean('is_active')->default(true); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hewan_qurban');
    }
};