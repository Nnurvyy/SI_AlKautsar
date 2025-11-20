<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kajian extends Model
{
    use HasFactory;

    protected $table = 'kajian';
    protected $primaryKey = 'id_kajian';

    protected $fillable = [
        'nama_penceramah',
        'tema_kajian',
        'jenis_kajian',
        'tanggal_kajian',
        'waktu_kajian',
        'foto_penceramah'
    ];

    protected $casts = [
        'tanggal_kajian' => 'date',
    ];

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

    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute()
    {
        return $this->foto_penceramah
            ? asset('storage/' . $this->foto_penceramah)
            : asset('images/default.png');
    }
}
