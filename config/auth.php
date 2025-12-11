<?php

return [

    
    'defaults' => [
        
        'guard' => env('AUTH_GUARD', 'jamaah'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'jamaahs'), 
    ],

    
    'guards' => [
        
        'jamaah' => [
            'driver' => 'session',
            'provider' => 'jamaahs',
        ],
        'pengurus' => [
            'driver' => 'session',
            'provider' => 'pengurus',
        ],
    ],

    
    'providers' => [
        'jamaahs' => [
            'driver' => 'eloquent',
            'model' => App\Models\Jamaah::class,
        ],
        'pengurus' => [
            'driver' => 'eloquent',
            'model' => App\Models\Pengurus::class,
        ],
    ],

    
    'passwords' => [
        
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

    
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
