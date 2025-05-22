<?php
namespace App\Providers\Filament;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\RequestResource;
use App\Filament\Resources\ItemResource;
use App\Filament\Resources\SupplierResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\ReportResource;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\Session\Middleware\AuthenticateSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ])
            ->authMiddleware([
                \Illuminate\Auth\Middleware\Authenticate::class,
            ])
            ->navigation(function (\Filament\Navigation\NavigationBuilder $navigation) {
                $navigation->groups([
                    \Filament\Navigation\NavigationGroup::make('Manajemen ATK')
                        ->items([
                            NavigationItem::make('Permintaan Barang')
                                ->icon(RequestResource::$navigationIcon)
                                ->url(fn () => RequestResource::getUrl('index'))
                                ->isActiveWhen(fn () => request()->routeIs('filament.resources.requests.*'))
                                ->visible(fn () => auth()->user()->hasRole(['staff', 'admin', 'pimpinan'])),
                            NavigationItem::make('Item')
                                ->icon(ItemResource::$navigationIcon)
                                ->url(fn () => ItemResource::getUrl('index'))
                                ->isActiveWhen(fn () => request()->routeIs('filament.resources.items.*'))
                                ->visible(fn () => auth()->user()->hasRole(['staff', 'admin', 'pimpinan'])),
                        ]),
                    \Filament\Navigation\NavigationGroup::make('Administrasi')
                        ->items([
                            NavigationItem::make('Pengguna')
                                ->icon(UserResource::$navigationIcon)
                                ->url(fn () => UserResource::getUrl('index'))
                                ->isActiveWhen(fn () => request()->routeIs('filament.resources.users.*'))
                                ->visible(fn () => auth()->user()->hasRole(['admin', 'pimpinan'])),
                            NavigationItem::make('Supplier')
                                ->icon(SupplierResource::$navigationIcon)
                                ->url(fn () => SupplierResource::getUrl('index'))
                                ->isActiveWhen(fn () => request()->routeIs('filament.resources.suppliers.*'))
                                ->visible(fn () => auth()->user()->hasRole(['admin', 'pimpinan'])),
                            NavigationItem::make('Laporan')
                                ->icon(ReportResource::$navigationIcon)
                                ->url(fn () => ReportResource::getUrl('index'))
                                ->isActiveWhen(fn () => request()->routeIs('filament.resources.reports.*'))
                                ->visible(fn () => auth()->user()->hasRole(['admin', 'pimpinan'])),
                        ]),
                ]);
            });
    }
}