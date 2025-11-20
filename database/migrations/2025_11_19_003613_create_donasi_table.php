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
        Schema::create('donasi', function (Blueprint $table) {
            // Sesuaikan Primary Key dengan Model ($primaryKey = 'id_donasi')
            $table->bigIncrements('id_donasi'); 

            // Sesuaikan Foreign Key dengan Controller ($request->id_program_donasi)
            $table->uuid('id_program_donasi');

            // Sesuaikan nama kolom dengan Controller & Model
            $table->string('nama_donatur');
            $table->bigInteger('nominal');
            $table->date('tanggal_donasi');
            $table->string('metode_pembayaran')->nullable(); 
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Foreign key ke tabel program_donasi (PK-nya 'id')
            $table->foreign('id_program_donasi')
                ->references('id')
                ->on('program_donasi')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasi');
    }
};