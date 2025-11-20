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
        Schema::create('artikel', function (Blueprint $table) {
            $table->uuid('id_artikel')->primary();
            $table->string('judul_artikel', 255); // Ditingkatkan agar sesuai validator
            $table->longtext('isi_artikel');
            $table->string('penulis_artikel', 100);
            $table->string('foto_artikel')->nullable(); // Foto Boleh Kosong
            $table->date('tanggal_terbit_artikel');
            // HAPUS kolom 'last_update_artikel'. Laravel akan menggunakan 'updated_at' secara otomatis.
            $table->string('status_artikel',50)->nullable();
            
            // Kolom created_at dan updated_at (pengganti last_update_artikel)
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Perbaiki nama tabel drop:
        Schema::dropIfExists('artikel');
    }
};