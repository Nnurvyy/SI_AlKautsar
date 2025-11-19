<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    use HasFactory;

    protected $table = 'donasi'; // Nama tabel di database
    protected $primaryKey = 'id_donasi'; // Primary Key

    protected $fillable = [
        'id_program_donasi',
        'nama_donatur',
        'nominal',
        'tanggal_donasi',
        'keterangan',
        'metode_pembayaran',
    ];

    // Relasi ke Program Donasi (Many to One)
    public function program()
    {
        return $this->belongsTo(ProgramDonasi::class, 'id_program_donasi');
    }
}