<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <--- Jangan lupa import ini

return new class extends Migration
{
    public function up(): void
    {
        // 1. KOSONGKAN TABEL DULU
        // Ini akan menghapus semua data agar tidak ada error saat nambah kolom baru
        DB::table('barang_inventaris')->truncate();

        Schema::table('barang_inventaris', function (Blueprint $table) {
            $table->dropColumn('kondisi');
            $table->renameColumn('stock', 'total_stock');
            
            // Sekarang aman pakai unique dan not null karena tabel kosong
            $table->string('kode', 5)->unique()->after('satuan'); 
        });
    }

    public function down(): void
    {
        Schema::table('barang_inventaris', function (Blueprint $table) {
            $table->string('kondisi', 50)->nullable();
            $table->renameColumn('total_stock', 'stock');
            $table->dropColumn('kode');
        });
    }
};