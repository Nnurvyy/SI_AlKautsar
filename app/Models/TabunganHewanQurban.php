<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TabunganHewanQurban extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tabungan_hewan_qurban';
    protected $primaryKey = 'id_tabungan_hewan_qurban';
    protected $fillable = [
        'nama_hewan',
        'total_hewan',
        'total_tabungan',
        'id_user',
        'total_harga_hewan_qurban'
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    // RELASI KE PEMASUKAN TABUNGAN QURBAN
    public function pemasukanTabunganQurban()
    {
        return $this->hasMany(PemasukanTabunganQurban::class, 'id_tabungan_hewan_qurban');
    }
}
