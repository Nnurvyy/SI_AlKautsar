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
        Schema::create('khotib_jumat', function (Blueprint $table) {
            $table->uuid('id_khotib_jumat')->primary();
            $table->string('nama_khotib_jumat', 100);
            $table->string('tema_khotib_jumat', 255);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khotib_jumat');
    }
};
