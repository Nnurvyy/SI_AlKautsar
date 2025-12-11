<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::create('khotib_jumat', function (Blueprint $table) {
            $table->uuid('id_khutbah')->primary();
            $table->string('nama_khotib', 100);
            $table->string('foto_khotib')->nullable();
            $table->string('nama_imam', 100);
            $table->string('tema_khutbah', 255);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

        public function down(): void
    {
        Schema::dropIfExists('khotib_jumat');
    }
};
