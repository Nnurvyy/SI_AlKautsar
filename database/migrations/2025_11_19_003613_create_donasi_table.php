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
            $table->id();

            // program_id mengikuti program_donasi yang pakai UUID
            $table->uuid('program_id');

            $table->string('nama');
            $table->bigInteger('nominal');
            $table->string('metode')->nullable();     // Transfer / QRIS / COD
            $table->text('pesan')->nullable();

            $table->timestamps();

            // foreign key
            $table->foreign('program_id')
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
