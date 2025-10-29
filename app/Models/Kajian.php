<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // <-- 1. Import class Str

class Kajian extends Model
{
    use HasFactory;

    protected $table = 'kajian';
    protected $primaryKey = 'id_kajian';
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'nama_penceramah',
        'tema_kajian',
        'tanggal_kajian',
        'waktu_kajian',
        'foto_penceramah',
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
}