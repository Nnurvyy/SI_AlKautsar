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
        'social_facebook',
        'social_instagram',
        'social_twitter',
        'social_youtube',
        'social_whatsapp',
    ];


    // Generate UUID otomatis
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function landingPage()
    {
        // Coba ambil data profil masjid jika model ada
        $masjidSettings = null;
        if (class_exists(\App\Models\MasjidProfil::class)) {
            $masjidSettings = \App\Models\MasjidProfil::first();
        }

        // Default fallback agar view tidak error
        if (!$masjidSettings) {
            $masjidSettings = (object)[
                'nama_masjid' => config('app.name', 'E‑Masjid'),
                'lokasi_nama' => 'Bandung',
                'foto_masjid'  => null, // view harus menangani null
            ];
        }

        return view('landing-page', compact('masjidSettings'));
    }
    
}
