<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Pengeluaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengeluaran';
    protected $primaryKey = 'id_pengeluaran';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['tanggal', 'nominal', 'id_kategori_pengeluaran', 'deskripsi'];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'integer',
    ];

    

    public function kategoriPengeluaran()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'id_kategori_pengeluaran');
    }



    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
