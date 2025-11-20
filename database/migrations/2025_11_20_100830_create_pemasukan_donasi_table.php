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
        Schema::create('pemasukan_donasi', function (Blueprint $table) {
            $table->uuid('id_pemasukan_donasi')->primary();

            // Foreign key ke donasi
            $table->uuid('id_donasi');
            $table->foreign('id_donasi')
                  ->references('id_donasi')
                  ->on('donasi')
                  ->onDelete('cascade');

            $table->date('tanggal');
            $table->string('nama_donatur');
            $table->bigInteger('nominal');
            $table->text('pesan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemasukan_donasi');
    }
};
