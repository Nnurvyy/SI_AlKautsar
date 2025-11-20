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
        'password',
        'google_id', // Tambahkan ini
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
