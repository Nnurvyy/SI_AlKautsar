<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProgramDonasi extends Model
{
    use HasFactory;

    protected $table = 'program_donasi';

    // Karena pakai UUID → tidak auto increment
    public $incrementing = false;
    protected $keyType = 'string';

    // AUTO GENERATE UUID SAAT INSERT
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // FIELD YANG BOLEH DI-INSERT
    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'target_dana',
        'dana_terkumpul',
        'tanggal_selesai'
    ];

    // Tambahan atribut otomatis
    protected $appends = ['persentase', 'sisa_hari', 'gambar_url'];

    public function donasi()
    {
        return $this->hasMany(Donasi::class, 'program_id');
    }

    // Hitung persentase donasi
    public function getPersentaseAttribute(): float
    {
        if ($this->target_dana > 0) {
            $persen = ($this->dana_terkumpul / $this->target_dana) * 100;
            return round(min($persen, 100), 2);
        }
        return 0;
    }

    // Hitung sisa hari
    public function getSisaHariAttribute(): int
    {
        if ($this->tanggal_selesai) {
            $sisa = Carbon::now()->diffInDays(Carbon::parse($this->tanggal_selesai), false);
            return max(0, $sisa);
        }
        return 0;
    }

    // URL gambar
    public function getGambarUrlAttribute(): string
    {
        $gambar = $this->gambar;

        if (!$gambar) {
            return 'https://via.placeholder.com/100';
        }

        if (Str::startsWith($gambar, 'http')) {
            return $gambar;
        }

        if (Str::startsWith($gambar, 'donasi/')) {
            return Storage::url($gambar);
        }

        return asset($gambar);
    }
}
