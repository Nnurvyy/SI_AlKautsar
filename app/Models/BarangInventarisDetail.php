<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BarangInventarisDetail extends Model
{
    use HasFactory;

    protected $table = 'barang_inventaris_detail';
    
    // Konfigurasi untuk UUID
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id_detail_barang';

    protected $fillable = [
        'id_barang',
        'kode_barang',
        'kondisi',
        'status',
        'deskripsi',
        'lokasi',
        'tanggal_masuk',
    ];
    
    // Hubungan ke Barang Inventaris Master (BarangInventaris)
    public function barangInventaris()
    {
        // Menggunakan nama Model yang sudah ada (BarangInventaris.php)
        return $this->belongsTo(BarangInventaris::class, 'id_barang', 'id_barang');
    }

    // Observer untuk auto-generate UUID dan update total_stock master
    protected static function boot()
    {
        parent::boot();

        // 1. Auto-generate UUID
        static::creating(function ($model) {
            if (! $model->{$model->getKeyName()}) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });

        // 2. Ketika detail baru dibuat, tambahkan 1 ke total_stock master
        static::created(function ($detail) {
            if ($detail->barangInventaris) {
                 $detail->barangInventaris->increment('total_stock');
            }
        });

        // 3. Ketika detail dihapus, kurangi 1 dari total_stock master
        static::deleted(function ($detail) {
            if ($detail->barangInventaris) {
                $detail->barangInventaris->decrement('total_stock');
            }
        });
    }
}