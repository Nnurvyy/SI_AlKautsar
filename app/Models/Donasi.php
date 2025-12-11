<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Donasi extends Model
{
    use HasFactory;

    protected $table = 'donasi';
    protected $primaryKey = 'id_donasi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_donasi',
        'nama_donasi',
        'foto_donasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'target_dana',
        'deskripsi',
    ];


    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getSisaHariAttribute()
    {
        if (!$this->tanggal_selesai) return null;

        $now = Carbon::now()->startOfDay();
        $end = Carbon::parse($this->tanggal_selesai)->startOfDay();


        if ($now->gt($end)) return 0;

        return $now->diffInDays($end, false);
    }


    public function pemasukan()
    {
        return $this->hasMany(PemasukanDonasi::class, 'id_donasi', 'id_donasi');
    }
}
