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
        Schema::create('program', function (Blueprint $table) {
            $table->uuid('id_program')->primary();
            $table->string('nama_program', 255);
            $table->string('penyelenggara_program', 150);
            $table->text('deskripsi_program');
            $table->dateTime('tanggal_program'); 
            $table->string('lokasi_program', 255);
            $table->string('foto_program')->nullable(); 
            $table->string('status_program');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program');
    }
};