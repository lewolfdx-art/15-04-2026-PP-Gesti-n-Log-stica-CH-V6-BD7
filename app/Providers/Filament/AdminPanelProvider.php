<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\TotalGeneralPagarWidget;
use App\Filament\Widgets\VentasPorMesChart;
use App\Filament\Widgets\ContratosPorVendedorChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\DatabaseBackupWidget;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('Concretera Mantaro')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('favicon.ico'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => view('filament.mapa-assets'))
            ->colors(\App\Support\ColoresPanel::paleta())

            // ========== GRUPOS DE NAVEGACIÓN CON ORDEN ==========
            ->navigationGroups([
                NavigationGroup::make()->label('Escritorio')->collapsible(false),
                NavigationGroup::make()->label('Navegación')->collapsible(false),
                NavigationGroup::make()->label('Gestión de Contenido'),
                NavigationGroup::make()->label('Usuarios'), // USUARIOS AQUÍ
                NavigationGroup::make()->label('Gestión de Operaciones'),
                NavigationGroup::make()->label('Comercial'),
                NavigationGroup::make()->label('Gestión de Personal'),
                NavigationGroup::make()->label('Finanzas'),
            ])
            
            // ========== BOTÓN "IR A LA WEB" ==========
            ->navigationItems([
                NavigationItem::make('Ir a la web')
                    ->url(url('/'))
                    ->icon('heroicon-o-globe-alt')
                    ->group('Navegación')
                    ->sort(-100),
            ])
            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                DatabaseBackupWidget::class,
                TotalGeneralPagarWidget::class,
                VentasPorMesChart::class,
                ContratosPorVendedorChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                // Igual que Authenticate de Filament, pero manda a los operadores a /operador
                \App\Http\Middleware\RedirigirOperadoresAlPanel::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->topbar(true);
    }
}
