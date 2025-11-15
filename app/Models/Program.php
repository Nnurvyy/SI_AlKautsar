<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    // Menggunakan trait HasUuids untuk secara otomatis menangani UUID
    use HasFactory, HasUuids;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'program';

    /**
     * Kolom primary key (kunci utama) untuk model.
     * Harus disetel secara eksplisit sebagai 'id_program'.
     *
     * @var string
     */
    protected $primaryKey = 'id_program';

    /**
     * Menunjukkan apakah primary key adalah auto-incrementing.
     * Disetel false karena menggunakan UUID.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipe data primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_program',
        'penyelenggara_program',
        'deskripsi_program',
        'tanggal_program',
        'lokasi_program',
        'foto_program',
        'status_program'
    ];

    /**
     * Kolom-kolom yang harus diubah ke tipe data tertentu (Casting).
     * Memastikan tanggal_program diperlakukan sebagai objek Carbon.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_program' => 'datetime',
    ];
    
    // --- Accessor (Opsional: Menghitung URL Foto) ---
    
    /**
     * Accessor untuk 'foto_url'. 
     * Memberikan URL lengkap ke aset foto program.
     */
    public function getFotoUrlAttribute()
    {
        return $this->foto_program 
            ? asset('storage/' . $this->foto_program) 
            : asset('images/default_program.png');
    }
    
    /**
     * Accessor yang akan ditambahkan ke array output model.
     *
     * @var array
     */
    protected $appends = ['foto_url'];
    
    // --- Scopes (Opsional: Memudahkan Query) ---
    
    /**
     * Scope untuk mengambil program yang belum selesai (masih akan datang).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('tanggal_program', '>=', now());
    }
}
