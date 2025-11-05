<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan import

class PemasukanTabunganQurban extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pemasukan_tabungan_qurban';
    protected $primaryKey = 'id_pemasukan_tabungan_qurban'; // <-- INI HARUSNYA PK ANDA

    /**
     * Tentukan kolom yang akan di-generate UUID-nya secara otomatis
     */
    public function uniqueIds()
    {
        // Pastikan nama kolom ini sesuai dengan Primary Key Anda
        return ['id_pemasukan_tabungan_qurban'];
    }

    protected $fillable = ['id_tabungan_hewan_qurban', 'tanggal', 'nominal'];


    // RELASI KE TABUNGAN HEWAN QURBAN
    public function tabunganHewanQurban()
    {
        return $this->belongsTo(TabunganHewanQurban::class, 'id_tabungan_hewan_qurban');
    }
}
