<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InfaqJumat extends Model
{
    use HasFactory;

    protected $table = 'infaq_jumat';
    protected $primaryKey = 'id_infaq_jumat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tanggal',
        'nominal'
    ];

    // Auto-generate UUID saat create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id_infaq_jumat) {
                $model->id_infaq_jumat = (string) Str::uuid();
            }
        });
    }
}
