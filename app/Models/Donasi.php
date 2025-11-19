<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    use HasFactory;

    protected $table = 'donasis';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'program_id',
        'nama_donatur',
        'jumlah'
    ];

    public function program()
    {
        return $this->belongsTo(ProgramDonasi::class, 'program_id');
    }
}
