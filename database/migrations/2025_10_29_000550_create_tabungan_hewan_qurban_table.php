<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tabungan_hewan_qurban', function (Blueprint $table) {
            $table->uuid('id_tabungan_hewan_qurban')->primary();
            $table->unsignedBigInteger('id_jamaah'); // Foreign key ke tabel 'jamaah' kolom 'id'

            $table->enum('nama_hewan', ['kambing', 'kerbau', 'domba', 'sapi', 'unta']);
            $table->integer('total_hewan')->default(0);

            // --- PERUBAHAN BARU: Menambah Jenis Tabungan dan Durasi ---
            $table->enum('saving_type', ['bebas', 'cicilan'])->default('cicilan'); // Tipe: bebas atau cicilan
            $table->integer('duration_months')->nullable(); // Durasi cicilan (misal: 12 bulan)
            // --- AKHIR PERUBAHAN BARU ---

            $table->bigInteger('total_tabungan')->default(0); // Sebaiknya dihapus, tapi saya biarkan karena sudah ada di file lama
            $table->bigInteger('total_harga_hewan_qurban')->default(0);
            $table->timestamps();

            // foreign key ke tabel 'jamaah' kolom 'id'
            $table->foreign('id_jamaah')->references('id')->on('jamaah')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tabungan_hewan_qurban');
    }
};
