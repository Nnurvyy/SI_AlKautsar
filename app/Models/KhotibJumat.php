<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KhotibJumat extends Model
{
    use HasFactory;

    protected $table = 'khotib_jumat';
    protected $primaryKey = 'id_khutbah';

    protected $fillable = [
        'nama_khotib',
        'nama_imam',
        'tema_khutbah',
        'tanggal',
        'foto_khotib',
    ];

    protected $casts = [
        'tanggal' => 'date',
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
        return $this->foto_khotib
            ? asset('storage/' . $this->foto_khotib)
            : asset('images/default.png');
    }

    public function getIsAktifAttribute()
    {
        if (!$this->tanggal) {
            return false;
        }
        return $this->tanggal->greaterThanOrEqualTo(Carbon::today());
    }
}
