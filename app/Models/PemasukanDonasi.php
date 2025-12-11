<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PemasukanDonasi extends Model
{
    use HasFactory;

    protected $table = 'pemasukan_donasi';
    protected $primaryKey = 'id_pemasukan_donasi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pemasukan_donasi',
        'id_donasi',
        'id_jamaah',
        'order_id',
        'tanggal',
        'nama_donatur',
        'metode_pembayaran',
        'nominal',
        'status',
        'tripay_reference',
        'checkout_url',
        'pesan',
    ];


    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }


    public function donasi()
    {
        return $this->belongsTo(Donasi::class, 'id_donasi', 'id_donasi');
    }

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'id_jamaah');
    }


    public function getAvatarUrlAttribute()
    {

        if ($this->id_jamaah && $this->jamaah && $this->jamaah->avatar) {
            return asset('storage/' . $this->jamaah->avatar);
        }


        return asset('images/default-user.png');
    }
}
