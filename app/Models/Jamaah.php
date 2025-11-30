<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Gunakan ini
use Illuminate\Notifications\Notifiable;

class Jamaah extends Authenticatable // extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Tentukan nama tabel secara eksplisit.
     */
    protected $table = 'jamaah';

    /**
     * Tentukan guard yang digunakan model ini.
     */
    protected $guard = 'jamaah';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'no_hp',        // <-- Baru
        'password',
        'google_id', 
        'avatar',
        'otp_code',     // <-- Baru
        'otp_expires_at', // <-- Baru
        'is_verified',  // <-- Baru
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime', // <-- Baru
        'otp_expires_at' => 'datetime',    // <-- Baru
        'is_verified' => 'boolean',        // <-- Baru
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=198754&color=fff';
    }

    public function pemasukanDonasi()
    {
        // hasMany(ModelTujuannya, 'foreign_key_di_tabel_tujuan', 'local_key_di_tabel_ini')
        return $this->hasMany(PemasukanDonasi::class, 'id_jamaah', 'id');
    }
}
