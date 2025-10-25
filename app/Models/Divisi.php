<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Divisi extends Model
{
    use HasFactory;

    protected $table = 'divisi';
    protected $primaryKey = 'id_divisi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_divisi',
        'deskripsi_divisi',
        'is_aktif',
    ];

    // Auto-generate UUID saat create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id_divisi) {
                $model->id_divisi = (string) Str::uuid();
            }
        });
    }
}
