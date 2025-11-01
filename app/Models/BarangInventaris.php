<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Diperlukan untuk Trait UUID

class BarangInventaris extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 'barang_inventaris';

    // Mematikan auto-increment karena menggunakan UUID
    public $incrementing = false;

    // Tipe data Primary Key (PK) adalah string/UUID
    protected $keyType = 'string';

    // Nama Primary Key
    protected $primaryKey = 'id_barang';


    /**
     * Atribut yang boleh diisi (mass assignable).
     * Sesuaikan dengan kebutuhan form input Anda.
     */
    protected $fillable = [
        'nama_barang',
        'satuan',
        'kondisi',
        'stock',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id_barang) {
                $model->id_barang = (string) Str::uuid();
            }
        });
    }
}