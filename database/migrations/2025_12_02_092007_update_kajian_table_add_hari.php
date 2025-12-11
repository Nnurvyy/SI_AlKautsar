<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kajian', function (Blueprint $table) {
            $table->string('hari', 20)->nullable()->after('tema_kajian');
            $table->date('tanggal_kajian')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kajian', function (Blueprint $table) {
            $table->dropColumn('hari');
            $table->date('tanggal_kajian')->nullable(false)->change();
        });
    }
};