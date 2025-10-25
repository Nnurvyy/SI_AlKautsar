<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // <-- 1. Import class Str

class PengeluaranKategori extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_kategori';
    protected $primaryKey = 'id_pengeluaran_kategori';
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'nama_pengeluaran_kategori',
    ];

    // --- Tambahan untuk UUID ---

    /**
     * 2. Beri tahu Eloquent bahwa Primary Key bukan auto-increment.
     */
    public $incrementing = false;

    /**
     * 3. Beri tahu Eloquent bahwa Primary Key adalah tipe string.
     */
    protected $keyType = 'string';

    /**
     * 4. Buat UUID secara otomatis saat membuat model baru.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            // Cek jika ID belum di-set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}