<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Panel para los operadores de vehículos y planta: ven solo las obras
 * que tienen asignadas (mapa, cómo llegar, fotos y viabilidad).
 */
class OperadorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('operador')
            ->path('operador')
            ->login()
            ->brandName('Concretera Mantaro')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::Orange,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => view('filament.mapa-assets'))
            ->discoverResources(in: app_path('Filament/Operador/Resources'), for: 'App\\Filament\\Operador\\Resources')
            ->pages([])
            // El asesor llega a sus cotizaciones; el operador, a sus obras
            ->homeUrl(fn () => auth()->user()?->hasRole('asesor') && ! auth()->user()->hasRole('operador')
                ? \App\Filament\Operador\Resources\MisCotizacionesResource::getUrl('index')
                : \App\Filament\Operador\Resources\MisObrasResource::getUrl('index'))
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
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
    }
}
