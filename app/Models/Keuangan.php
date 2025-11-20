<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Keuangan extends Model
{
    use HasFactory;

    protected $table = 'keuangan';
    protected $primaryKey = 'id_keuangan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_keuangan',
        'tipe',
        'tanggal',
        'nominal',
        'id_kategori_keuangan',
        'deskripsi',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate UUID otomatis
        static::creating(function ($model) {
            if (empty($model->id_keuangan)) {
                $model->id_keuangan = (string) Str::uuid();
            }
        });
    }

    // Relasi ke KategoriKeuangan (Many to One)
    public function kategori()
    {
        return $this->belongsTo(KategoriKeuangan::class, 'id_kategori_keuangan', 'id_kategori_keuangan');
    }
}
