<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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

    // ======================
    // 🔗 RELATIONSHIPS
    // ======================

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function kategori()
    {
        return $this->belongsTo(PengeluaranKategori::class, 'id_kategori', 'id_kategori');
    }
}
