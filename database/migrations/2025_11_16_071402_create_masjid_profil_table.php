<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::create('masjid_profil', function (Blueprint $table) {
            $table->uuid('id_masjid')->primary();
            $table->string('nama_masjid')->default('Nama Masjid');
            $table->string('foto_masjid')->nullable();
            $table->string('lokasi_nama')->nullable(); 
            $table->string('lokasi_id_api')->nullable(); 
            $table->string('lokasi_nama_api')->nullable();
            $table->text('deskripsi_masjid')->nullable();
            $table->decimal('latitude', 10, 8)->nullable()->after('lokasi_nama_api');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('social_facebook')->nullable();
            $table->string('social_instagram')->nullable();
            $table->string('social_twitter')->nullable();
            $table->string('social_youtube')->nullable();
            $table->string('social_whatsapp')->nullable();
            $table->timestamps();
        });
    }

        public function down(): void
    {
        Schema::dropIfExists('masjid_profil');
    }
};
