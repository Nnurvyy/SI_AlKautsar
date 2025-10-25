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
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id_students')->primary();
            $table->char('nis', 9)->unique();
            $table->string('nama', 100);
            $table->char('kelas', 15)->nullable();
            $table->char('nomor_telepon', 14)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('nama_ibu', 100)->nullable();
            $table->string('nama_ayah', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
