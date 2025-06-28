<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\RequestsChart;
use App\Filament\Widgets\NotificationBell;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // Kategori 1: Konfigurasi Dasar Panel Admin
            ->default() // Menjadikan panel ini sebagai panel default
            ->id('admin') // ID panel adalah 'admin'
            ->path('admin') // URL akses panel di '/admin'
            ->login() // Mengaktifkan halaman login
            ->brandName('Sistem ATK PTA Jayapura') // Nama merek aplikasi
            ->authGuard('web') // Menggunakan guard 'web' untuk autentikasi
            ->colors([
                'primary' => '#22c55e', // Warna utama panel adalah Amber
            ])

            // Kategori 2: Pengelolaan Sumber Daya (Resources)
            ->discoverResources(
                in: app_path('Filament/Resources'), 
                for: 'App\\Filament\\Resources'
            ) // Menemukan semua resource di direktori app/Filament/Resources

            // Kategori 3: Pengelolaan Halaman (Pages)
            ->discoverPages(
                in: app_path('Filament/Pages'), 
                for: 'App\\Filament\\Pages'
            ) // Menemukan semua halaman di direktori app/Filament/Pages
            ->pages([
                Pages\Dashboard::class, // Menambahkan halaman dashboard
            ])

            // Kategori 4: Widget untuk Dashboard
            ->widgets([
                StatsOverview::class, // Widget untuk ringkasan statistik
                RequestsChart::class, // Widget untuk grafik permintaan
                Widgets\AccountWidget::class, // Widget untuk informasi akun
                NotificationBell::class, // Widget notifikasi permintaan ATK
            ])

            
            // Kategori 5: Middleware untuk Keamanan dan Fungsionalitas
            ->middleware([
                EncryptCookies::class, // Mengenkripsi cookie
                AddQueuedCookiesToResponse::class, // Menambahkan cookie ke respons
                StartSession::class, // Memulai sesi pengguna
                AuthenticateSession::class, // Memastikan sesi pengguna valid
                ShareErrorsFromSession::class, // Membagikan error ke view
                VerifyCsrfToken::class, // Verifikasi token CSRF
                SubstituteBindings::class, // Mengganti binding route
                DisableBladeIconComponents::class, // Nonaktifkan ikon Blade
                DispatchServingFilamentEvent::class, // Kirim event Filament
            ])

            // Kategori 6: Middleware Autentikasi
            ->authMiddleware([
                Authenticate::class, // Memastikan pengguna login
            ]);
    }
}