<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Kajian extends Model
{
    use HasFactory;

    protected $table = 'kajian';

    protected $primaryKey = 'id_kajian';

    protected $fillable = [
        'tipe',
        'nama_penceramah',
        'tema_kajian',
        'tanggal_kajian',
        'hari',
        'waktu_kajian',
        'foto_penceramah',
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



    protected $appends = ['foto_url', 'is_aktif'];

    public function getFotoUrlAttribute()
    {
        return $this->foto_penceramah
            ? asset('storage/' . $this->foto_penceramah)
            : asset('images/default.png');
    }

    public function getIsAktifAttribute()
    {

        if ($this->tipe === 'rutin') {
            return true;
        }


        if (!$this->tanggal_kajian) {
            return false;
        }


        return $this->tanggal_kajian->greaterThanOrEqualTo(Carbon::today());
    }

    public function getJadwalLengkapAttribute()
    {
        if ($this->tipe == 'rutin') {
            return "Setiap Hari " . ucfirst($this->hari);
        }


        return \Carbon\Carbon::parse($this->tanggal_kajian)->translatedFormat('d F Y');
    }
}
