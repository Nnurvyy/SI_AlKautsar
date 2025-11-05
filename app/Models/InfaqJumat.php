<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InfaqJumat extends Model
{
    use HasFactory;

    protected $table = 'infaq_jumat';
    // Primary Key disetel ke nama yang benar
    protected $primaryKey = 'id_infaq'; // Saya ganti ke 'id' sebagai standar, atau pertahankan 'id_infaq_jumat'
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tanggal_infaq',
        'nominal_infaq' // DIPERBAIKI: Mengikuti nama field di View/Controller
    ];
    
    // Casting agar 'tanggal_infaq' selalu menjadi Carbon instance
    protected $casts = [
        'tanggal_infaq' => 'date', 
    ];

    // Auto-generate UUID saat create (Menggunakan Primary Key 'id' atau 'id_infaq_jumat'
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Menggunakan $model->id, $model->{$model->getKeyName()} atau $model->id_infaq_jumat (sesuai primaryKey)
            if (! $model->{$model->getKeyName()}) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
