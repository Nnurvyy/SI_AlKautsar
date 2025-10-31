<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // <-- 1. Import class Str

class KhotibJumat extends Model
{
    use HasFactory;

    protected $table = 'khotib_jumat';
    protected $primaryKey = 'id_khutbah';


    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'nama_khotib',
        'nama_imam',
        'tema_khutbah',
        'tanggal',
        'foto_khotib',
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
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute()
    {
        return $this->foto_khotib
            ? asset('storage/' . $this->foto_khotib)
            : asset('images/default.png');
    }
}