return [
    'defaults' => [
        'guard' => 'web', // Guard default untuk pengguna biasa
        'passwords' => 'users', // Default untuk reset password
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users', // Guard web untuk pengguna biasa
        ],
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins', // Guard terpisah untuk admin
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class, // Model default untuk pengguna biasa
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class, // Model untuk admin
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_resets', // Bisa menggunakan tabel yang sama atau terpisah
            'expire' => 60,
            'throttle' => 60,
        ],
    ],
];