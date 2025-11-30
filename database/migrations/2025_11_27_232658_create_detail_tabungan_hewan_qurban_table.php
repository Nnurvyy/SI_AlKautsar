<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_tabungan_hewan_qurban', function (Blueprint $table) {
            $table->id(); // ID Auto increment untuk detail internal
            $table->uuid('id_tabungan_hewan_qurban'); // Foreign key ke tabungan (UUID)
            $table->uuid('id_hewan_qurban'); // Foreign key ke hewan (UUID)
            
            $table->integer('jumlah_hewan'); // Jumlah ekor per jenis ini
            $table->bigInteger('harga_per_ekor'); // Harga snapshot saat transaksi dibuat (PENTING)
            $table->bigInteger('subtotal'); // jumlah * harga_per_ekor
            
            $table->timestamps();

            // Definisi Foreign Key
            $table->foreign('id_tabungan_hewan_qurban')
                  ->references('id_tabungan_hewan_qurban')
                  ->on('tabungan_hewan_qurban')
                  ->onDelete('cascade');
                  
            $table->foreign('id_hewan_qurban')
                  ->references('id_hewan_qurban')
                  ->on('hewan_qurban')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_tabungan_hewan_qurban');
    }
};