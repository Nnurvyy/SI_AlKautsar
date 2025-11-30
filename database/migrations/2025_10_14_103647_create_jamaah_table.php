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
        Schema::create('jamaah', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('no_hp');
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable(); // Waktu kadaluarsa OTP
            $table->boolean('is_verified')->default(false); // Status verifikasi
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Nullable untuk login Google
            $table->string('google_id')->nullable(); // Untuk ID Google
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jamaah');
    }
};
