<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Student; 
use App\Models\PemasukanKategori; 
use App\Models\Divisi;

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';
    protected $primaryKey = 'id_pemasukan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pemasukan',
        'id_divisi',
        'id_siswa',
        'id_kategori',
        'metode_pembayaran',
        'nominal',
        'deskripsi',
        'tanggal_transaksi',
        'nomor_kwitansi',
    ];

    // Relasi ke siswa
    public function siswa()
    {
        return $this->belongsTo(Student::class, 'id_siswa');
    }

    // Relasi ke kategori pemasukan
    public function kategori()
    {
        return $this->belongsTo(PemasukanKategori::class, 'id_kategori');
    }

    // Relasi ke divisi
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }

    // Generate UUID otomatis
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
