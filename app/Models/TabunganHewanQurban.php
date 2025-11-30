<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'status', 
        'tanggal_pembuatan',
        'saving_type', 
        'duration_months', 
        'total_tabungan', 
        'total_harga_hewan_qurban'
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }

            if (empty($model->tanggal_pembuatan)) {
                $model->tanggal_pembuatan = now()->toDateString();
            }
        });
    }

    // Relasi ke User/Jamaah
    public function jamaah() {
        return $this->belongsTo(Jamaah::class, 'id_jamaah', 'id');
    }

    // Relasi ke Detail (Barang/Hewan apa saja yang dibeli)
    public function details() {
        return $this->hasMany(DetailTabunganHewanQurban::class, 'id_tabungan_hewan_qurban', 'id_tabungan_hewan_qurban');
    }
    
    public function pemasukanTabunganQurban() {
        return $this->hasMany(PemasukanTabunganQurban::class, 'id_tabungan_hewan_qurban', 'id_tabungan_hewan_qurban');
    }
    
    // Helper untuk cek apakah sudah lunas
    public function getIsLunasAttribute() {
        // Hitung manual dari relasi agar akurat
        $terkumpul = $this->pemasukanTabunganQurban()->sum('nominal');
        return $terkumpul >= $this->total_harga_hewan_qurban;
    }
}