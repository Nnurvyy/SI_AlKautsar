<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PemasukanKategori extends Model // 🔥 ubah dari KategoriPemasukan ke PemasukanKategori
{
    use HasFactory;

    protected $table = 'kategori_pemasukan';
    protected $primaryKey = 'id_kategori_pemasukan';
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_kategori_pemasukan',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
