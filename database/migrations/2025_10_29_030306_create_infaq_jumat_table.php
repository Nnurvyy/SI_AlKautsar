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
        Schema::create('infaq_jumat', function (Blueprint $table) {
            $table->uuid('id_infaq_jumat')->primary();
            $table->date('tanggal_infaq');
            // PERBAIKAN: Mengganti $table->int('nominal') menjadi $table->bigInteger('nominal')
            $table->bigInteger('nominal_infaq');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infaq_jumat');
    }
};
