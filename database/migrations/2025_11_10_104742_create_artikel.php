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
            Schema::create('artikel', function (Blueprint $table) {
                $table->uuid('id_artikel')->primary();
                $table->string('judul_artikel', 100);
                $table->longtext('isi_artikel');
                $table->string('penulis_artikel', 100);
                $table->string('foto_artikel')->nullable();
                $table->date('tanggal_terbit_artikel');
                $table->date('last_update_artikel');
                $table->string('status_artikel',50)->nullable();
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
