<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon; 

class Kajian extends Model
{
    use HasFactory;

    /**
     * Ganti nama tabel
     */
    protected $table = 'kajian';
    
    /**
     * Ganti primary key
     */
    protected $primaryKey = 'id_kajian';

    /**
     * Sesuaikan kolom-kolom yang bisa diisi
     */
    protected $fillable = [
        'nama_penceramah',
        'tema_kajian',
        'tanggal_kajian',
        'waktu_kajian',
        'foto_penceramah',
    ];

    /**
     * Sesuaikan kolom tanggal
     */
    protected $casts = [
        'tanggal_kajian' => 'date',
    ];

    // --- Ini untuk UUID (Biarkan saja, sudah benar) ---
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
    // --- Selesai UUID ---


    /**
     * (PENTING) Biarkan ini agar 'foto_url' dan 'is_aktif' bisa dipakai di JS/Blade
     */
    protected $appends = ['foto_url', 'is_aktif'];

    /**
     * (PENTING) Ganti 'foto_khotib' menjadi 'foto_penceramah'
     */
    public function getFotoUrlAttribute()
    {
        return $this->foto_penceramah
            ? asset('storage/' . $this->foto_penceramah)
            : asset('images/default.png'); // Pastikan kamu punya default.png di public/images
    }

    /**
     * (PENTING) Ganti 'tanggal' menjadi 'tanggal_kajian'
     */
    public function getIsAktifAttribute()
    {
        if (!$this->tanggal_kajian) {
            return false;
        }
        // Bandingkan 'tanggal_kajian' dengan hari ini
        return $this->tanggal_kajian->greaterThanOrEqualTo(Carbon::today());
    }
}
