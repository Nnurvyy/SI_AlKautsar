<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Pemasukan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pemasukan';
    protected $primaryKey = 'id_pemasukan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_divisi',
        'id_kategori',
        'id_students',
        'nominal',
        'deskripsi',
        'tanggal_transaksi',
        'nomor_kwitansi',
        'penerima',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'nominal' => 'integer',
    ];

    // ======================
    // 🔗 RELATIONSHIPS
    // ======================

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function kategori()
    {
        return $this->belongsTo(PemasukanKategori::class, 'id_kategori', 'id_kategori');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'id_students', 'id_students');
    }
}
