<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Diperlukan untuk Str::uuid()
use Carbon\Carbon; // Dipertahankan (walau tidak dipakai langsung, berguna untuk casts)

class Artikel extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'artikel';

    /**
     * Kolom primary key (kunci utama) untuk model.
     *
     * @var string
     */
    protected $primaryKey = 'id_artikel';

    /**
     * Menunjukkan apakah primary key adalah auto-incrementing (default: true).
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipe data primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul_artikel',
        'isi_artikel',
        'penulis_artikel',
        'foto_artikel',
        'tanggal_terbit_artikel',
        'last_update_artikel',
        'status_artikel',
    ];

    /**
     * Kolom-kolom yang harus diubah ke tipe data tertentu (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_terbit_artikel' => 'date',
        'last_update_artikel' => 'date',
    ];

    /**
     * Accessor yang akan ditambahkan ke array output model.
     * Hanya menyertakan 'foto_url'.
     *
     * @var array
     */
    protected $appends = ['foto_url'];

    // --- Booting Method untuk UUID ---

    /**
     * Method yang dipanggil saat model 'boot' (dijalankan).
     * Membuat UUID jika primary key kosong sebelum proses 'creating'.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // --- Accessor (Getter) ---

    /**
     * Accessor untuk 'foto_url'
     * Mendapatkan URL lengkap untuk foto artikel.
     */
    public function getFotoUrlAttribute()
    {
        // Sesuaikan 'images/default_artikel.png' jika nama file default berbeda
        return $this->foto_artikel
            ? asset('storage/' . $this->foto_artikel)
            : asset('images/default_artikel.png');
    }

}