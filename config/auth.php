return [
    'defaults' => [
        'guard' => 'web', // String, bukan integer
        'passwords' => 'users', // String, bukan integer
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60, // Integer, ini valid
            'throttle' => 60, // Integer, ini valid
        ],
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_resets',
            'expire' => 60, // Integer, ini valid
            'throttle' => 60, // Integer, ini valid
        ],
    ],
];