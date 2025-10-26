<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Student; 
use App\Models\PemasukanKategori; 

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';
    protected $primaryKey = 'id_pemasukan';
    public $incrementing = false; // karena pakai UUID
    protected $keyType = 'string';

    protected $fillable = [
        'id_pemasukan',
        'tanggal',
        'jumlah',
        'keterangan',
        'id_students',
        'id_pemasukan_kategori',
    ];

    // Relasi ke Students
    public function student()
    {
    return $this->belongsTo(Student::class, 'id_students');
    }

    // Relasi ke Kategori Pemasukan
    public function kategori()
    {
        return $this->belongsTo(PemasukanKategori::class, 'id_pemasukan_kategori');
    }

    //  Auto generate UUID saat create
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
