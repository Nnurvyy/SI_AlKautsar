<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        // Kita set default ke jamaah, tapi logic login akan custom
        'guard' => env('AUTH_GUARD', 'jamaah'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'jamaahs'), // Ganti ke jamaahs
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        // Tambahkan guard baru
        'jamaah' => [
            'driver' => 'session',
            'provider' => 'jamaahs',
        ],
        'pengurus' => [
            'driver' => 'session',
            'provider' => 'pengurus',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        // Hapus provider 'pengguna' lama
        // 'pengguna' => [
        //     'driver' => 'eloquent',
        //     'model' => env('AUTH_MODEL', App\Models\Pengguna::class),
        // ],

        // Tambahkan provider baru
        'jamaahs' => [
            'driver' => 'eloquent',
            'model' => App\Models\Jamaah::class,
        ],
        'pengurus' => [
            'driver' => 'eloquent',
            'model' => App\Models\Pengurus::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        // Sesuaikan dengan provider baru
        'jamaahs' => [
            'provider' => 'jamaahs',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
        'pengurus' => [
            'provider' => 'pengurus',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
