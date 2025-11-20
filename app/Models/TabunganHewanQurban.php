<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabunganHewanQurban extends Model
{
    use HasFactory;

    protected $table = 'tabungan_hewan_qurban';
    protected $primaryKey = 'id_tabungan_hewan_qurban';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_tabungan_hewan_qurban',
        'id_jamaah',
        'nama_hewan',
        'total_hewan',
        'total_tabungan',
        'total_harga_hewan_qurban',
        // --- TAMBAHAN BARU ---
        'saving_type',
        'duration_months',
        // --- AKHIR TAMBAHAN BARU ---
    ];

    // Relasi ke Jamaah
    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class, 'id_jamaah', 'id');
    }

    // Relasi ke Pemasukan
    public function pemasukanTabunganQurban()
    {
        return $this->hasMany(PemasukanTabunganQurban::class, 'id_tabungan_hewan_qurban', 'id_tabungan_hewan_qurban');
    }
}
