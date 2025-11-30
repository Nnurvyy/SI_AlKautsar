<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Program extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'program';
    protected $primaryKey = 'id_program';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nama_program',
        'penyelenggara_program',
        'deskripsi_program',
        'tanggal_program',
        'lokasi_program',
        'foto_program',
        'status_program'
    ];

    protected $casts = [
        'tanggal_program' => 'datetime',
    ];

    // Tambahkan atribut virtual agar bisa diakses di JS
    protected $appends = ['foto_url'];

    /**
     * Accessor untuk URL Foto
     * Jika tidak ada foto, tampilkan placeholder default
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto_program) {
            return asset('storage/' . $this->foto_program);
        }
        // Pastikan Anda memiliki gambar ini di public/images/ atau ganti namanya
        return asset('images/default_program.png'); 
    }
}