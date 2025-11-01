<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Student; 
use App\Models\PemasukanKategori; 
use App\Models\Divisi;

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';
    protected $primaryKey = 'id_pemasukan';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['tanggal', 'nominal', 'id_kategori_pemasukan', 'deskripsi'];

    // Relasi ke kategori pemasukan
    public function kategoriPemasukan()
    {
        return $this->belongsTo(KategoriPemasukan::class, 'id_kategori_pemasukan');
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
