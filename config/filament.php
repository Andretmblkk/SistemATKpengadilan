<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Styles
    |--------------------------------------------------------------------------
    */
    'styles' => [
        'resources/css/filament/custom-login.css',
    ],

    /*
    |--------------------------------------------------------------------------
    | Brand
    |--------------------------------------------------------------------------
    */
    'brand' => [
        'logo' => '/images/logo.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'guard' => 'web', // Guard autentikasi Laravel
        'pages' => [
            'login' => \Filament\Pages\Auth\Login::class, // Untuk Filament v3
            // Jika menggunakan Filament v2, gunakan:
            // 'login' => \Filament\Http\Livewire\Auth\Login::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    */
    'middleware' => [
        'auth' => [
            \Illuminate\Auth\Middleware\Authenticate::class,
        ],
        'base' => [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Filament\Http\Middleware\DisableBladeIconCache::class,
            \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    */
    'broadcasting' => [
        // 'echo' => [
        //     'broadcaster' => 'pusher',
        //     'key' => env('VITE_PUSHER_APP_KEY'),
        //     'cluster' => env('VITE_PUSHER_APP_CLUSTER'),
        //     'forceTLS' => true,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */
    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),
];