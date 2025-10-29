<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\PemasukanKategori; // sesuai class baru di bawah

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';
    protected $primaryKey = 'id_pemasukan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pemasukan',
        'tanggal',
        'nominal',
        'id_kategori_pemasukan',
        'deskripsi',
    ];

    // Relasi ke kategori pemasukan
    public function kategori()
    {
        return $this->belongsTo(PemasukanKategori::class, 'id_kategori_pemasukan', 'id_kategori_pemasukan');
    }

    // Generate UUID otomatis
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
