<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
     * Telah diperbarui: 'foto_artikel' diganti menjadi 'foto_url' (sesuai kolom database).
     * 'last_update_artikel' dihapus, biarkan timestamps Laravel yang menangani.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul_artikel',
        'isi_artikel',
        'penulis_artikel',
        'foto_artikel', // ← ganti balik ke nama kolom migration
        'tanggal_terbit_artikel',
        'status_artikel',
    ];


    /**
     * Kolom-kolom yang harus diubah ke tipe data tertentu (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_terbit_artikel' => 'date:d-m-y',
        'last_update_artikel' => 'date',
    ];
    
    // Nonaktifkan default timestamps (created_at dan updated_at) jika tabel Anda tidak memilikinya
    // public $timestamps = false; 
    // CATATAN: Jika Anda menggunakan last_update_artikel, nonaktifkan $timestamps dan 
    // pastikan kolom tersebut diisi di controller, atau gunakan updated_at.

    
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
    public function getFotoArtikelAttribute($value)
    {
        return $value ? asset('storage/' . $value) 
                    : asset('images/default_artikel.png');
    }

    


}