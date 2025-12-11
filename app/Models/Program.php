<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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


    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute()
    {
        if ($this->foto_program) {
            return asset('storage/' . $this->foto_program);
        }

        return asset('images/default_program.png');
    }
}
