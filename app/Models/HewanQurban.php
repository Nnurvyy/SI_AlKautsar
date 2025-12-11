<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HewanQurban extends Model
{
    use HasFactory;

    protected $table = 'hewan_qurban';
    protected $primaryKey = 'id_hewan_qurban';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_hewan_qurban',
        'nama_hewan',
        'kategori_hewan',
        'harga_hewan',
        'is_active'
    ];


    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
