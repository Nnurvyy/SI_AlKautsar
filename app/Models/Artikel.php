<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Artikel extends Model
{
    use HasFactory;

    protected $table = 'artikel';
    protected $primaryKey = 'id_artikel';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'judul_artikel',
        'isi_artikel',
        'penulis_artikel',
        'foto_artikel',
        'tanggal_terbit_artikel',
        'status_artikel',
    ];

    protected $casts = [
        'tanggal_terbit_artikel' => 'date',
        'last_update_artikel' => 'date',
    ];


    protected $appends = ['foto_url'];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getFotoUrlAttribute()
    {

        $foto = $this->attributes['foto_artikel'] ?? null;

        return $foto
            ? asset('storage/' . $foto)
            : asset('images/default_artikel.png');
    }
}
