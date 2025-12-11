<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::table('jamaah', function (Blueprint $table) {
            
            $table->string('no_hp')->nullable()->change();
        });
    }

        public function down(): void
    {
        Schema::table('jamaah', function (Blueprint $table) {
            
            $table->string('no_hp')->nullable(false)->change();
        });
    }
};