<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BarangInventaris extends Model
{
    use HasFactory;


    protected $table = 'barang_inventaris';


    public $incrementing = false;


    protected $keyType = 'string';


    protected $primaryKey = 'id_barang';


    protected $fillable = [
        'nama_barang',
        'satuan',
        'kondisi',
        'stock',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            if (! $model->{$model->getKeyName()}) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
