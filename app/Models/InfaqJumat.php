<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Support\Str;
=======
use Illuminate\Support\Str; // <-- 1. Import class Str
>>>>>>> eeddd5f44f2f6e40c27d20ea7c0b8844ef6d12d1

class InfaqJumat extends Model
{
    use HasFactory;

    protected $table = 'infaq_jumat';
<<<<<<< HEAD
    protected $primaryKey = 'id_infaq_jumat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tanggal',
        'nominal'
    ];

    // Auto-generate UUID saat create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id_infaq_jumat) {
                $model->id_infaq_jumat = (string) Str::uuid();
            }
        });
    }
}
=======
    protected $primaryKey = 'id_infaq';
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = ['tanggal', 'nominal'];

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
>>>>>>> eeddd5f44f2f6e40c27d20ea7c0b8844ef6d12d1
