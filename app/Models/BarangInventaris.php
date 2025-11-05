<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangInventaris extends Model
{
    use HasFactory; // AKTIFKAN HasUuids

    // Nama tabel
    protected $table = 'barang_inventaris';

    // Mematikan auto-increment karena menggunakan UUID (WAJIB)
    public $incrementing = false;

    // Tipe data Primary Key (PK) adalah string/UUID (WAJIB)
    protected $keyType = 'string';

    // Nama Primary Key (WAJIB)
    protected $primaryKey = 'id_barang'; 
    // Catatan: Model akan secara otomatis mengisi UUID saat create/store berkat HasUuids

    /**
     * Atribut yang boleh diisi (mass assignable).
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
            // Menggunakan $model->id, $model->{$model->getKeyName()} atau $model->id_infaq_jumat (sesuai primaryKey)
            if (! $model->{$model->getKeyName()}) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
