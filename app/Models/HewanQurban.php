<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HewanQurban extends Model
{
    use HasFactory;

    protected $table = 'hewan_qurban';
    protected $primaryKey = 'id_hewan_qurban';
    public $incrementing = false; // Karena UUID
    protected $keyType = 'string';
    
    protected $fillable = [
        'id_hewan_qurban', 
        'nama_hewan', 
        'kategori_hewan', 
        'harga_hewan', 
        'is_active'
    ];

    // Boot function untuk generate UUID otomatis saat create
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Scope untuk mengambil hanya hewan yang dijual
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}