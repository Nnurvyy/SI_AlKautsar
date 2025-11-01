<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inventori extends Model
{
    use HasFactory;

    protected $table = 'inventori';
    protected $primaryKey = 'id_barang';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'nama_barang',
        'jumlah_barang',
        'nama_kategori_pemasukan',
    ];

    /**
     * Primary key bukan auto-increment.
     */
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
