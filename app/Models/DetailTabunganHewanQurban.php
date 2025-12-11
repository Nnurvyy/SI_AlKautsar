<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTabunganHewanQurban extends Model
{
    protected $table = 'detail_tabungan_hewan_qurban';

    protected $fillable = [
        'id_tabungan_hewan_qurban',
        'id_hewan_qurban',
        'jumlah_hewan',
        'harga_per_ekor',
        'subtotal'
    ];


    public function tabungan()
    {
        return $this->belongsTo(TabunganHewanQurban::class, 'id_tabungan_hewan_qurban', 'id_tabungan_hewan_qurban');
    }


    public function hewan()
    {
        return $this->belongsTo(HewanQurban::class, 'id_hewan_qurban', 'id_hewan_qurban');
    }
}
