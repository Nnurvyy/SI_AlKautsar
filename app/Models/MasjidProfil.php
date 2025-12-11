<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class MasjidProfil extends Model
{
    use HasFactory;

    protected $table = 'masjid_profil';
    protected $primaryKey = 'id_masjid';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'nama_masjid',
        'foto_masjid',
        'lokasi_nama',
        'lokasi_id_api',
        'lokasi_nama_api',
        'deskripsi_masjid',
        'latitude',
        'longitude',
        'social_facebook',
        'social_instagram',
        'social_twitter',
        'social_youtube',
        'social_whatsapp',
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
