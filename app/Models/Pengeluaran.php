<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Pengeluaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengeluaran';
    protected $primaryKey = 'id_pengeluaran';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_divisi',
        'id_kategori',
        'nominal',
        'deskripsi',
        'tanggal_transaksi',
        'nomor_kwitansi',
        'penanggung_jawab',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'nominal' => 'integer',
    ];

    

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function kategori()
    {
        return $this->belongsTo(PengeluaranKategori::class, 'id_kategori', 'id_kategori');
    }



    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
