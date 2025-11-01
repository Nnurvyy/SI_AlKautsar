<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PemasukanTabunganQurban extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pemasukan_tabungan_qurban';
    protected $primaryKey = 'id_pemasukan_tabungan_qurban';
    protected $fillable = ['id_pengguna', 'tanggal', 'nominal'];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    // RELASI KE TABUNGAN HEWAN QURBAN
    public function tabunganHewanQurban()
    {
        return $this->belongsTo(TabunganHewanQurban::class, 'id_tabungan_hewan_qurban');
    }
}
