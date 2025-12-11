<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Jamaah extends Authenticatable
{
    use HasFactory, Notifiable;


    protected $table = 'jamaah';

    protected $guard = 'jamaah';

    protected $fillable = [
        'name',
        'email',
        'no_hp',
        'password',
        'google_id',
        'avatar',
        'otp_code',
        'otp_expires_at',
        'is_verified',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'is_verified' => 'boolean',
        'password' => 'hashed',
    ];

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
        return $this->hasMany(PemasukanDonasi::class, 'id_jamaah', 'id');
    }
}
