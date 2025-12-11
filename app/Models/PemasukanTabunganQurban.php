<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class PemasukanTabunganQurban extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pemasukan_tabungan_qurban';
    protected $primaryKey = 'id_pemasukan_tabungan_qurban';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pemasukan_tabungan_qurban',
        'id_tabungan_hewan_qurban',
        'order_id',
        'tripay_reference',
        'checkout_url',
        'tanggal',
        'nominal',
        'metode_pembayaran',
        'status'
    ];


    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function tabunganHewanQurban()
    {
        return $this->belongsTo(TabunganHewanQurban::class, 'id_tabungan_hewan_qurban');
    }
}
