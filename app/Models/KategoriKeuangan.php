<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KategoriKeuangan extends Model
{
    use HasFactory;

    protected $table = 'kategori_keuangan';
    protected $primaryKey = 'id_kategori_keuangan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_kategori_keuangan',
        'nama_kategori_keuangan',
        'tipe',
    ];

    protected static function boot()
    {
        parent::boot();


        static::creating(function ($model) {
            if (empty($model->id_kategori_keuangan)) {
                $model->id_kategori_keuangan = (string) Str::uuid();
            }
        });
    }


    public function keuangan()
    {
        return $this->hasMany(Keuangan::class, 'id_kategori_keuangan', 'id_kategori_keuangan');
    }
}
