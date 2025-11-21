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
        Schema::create('kajian', function (Blueprint $table) {
            $table->uuid('id_kajian')->primary();
            $table->enum('tipe', ['rutin', 'event']);
            $table->string('nama_penceramah', 100);
            $table->string('tema_kajian', 255);
            $table->date('tanggal_kajian');
            $table->time('waktu_kajian')->nullable();
            $table->string('foto_penceramah')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kajian');
    }
};
