<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Respaldo de la BD cada 14 días sin programador de tareas: cuando un administrador
 * abre el Escritorio y el último respaldo tiene 14 días o más, se genera uno nuevo
 * después de enviar la página. Funciona igual en local y en el hosting.
 */
class RespaldoAutomatico
{
    public const CADA_DIAS = 14;

    public static function ultimo(): ?Carbon
    {
        $archivos = glob(storage_path('app/backups/backup_*.zip')) ?: [];

        if ($archivos === []) {
            return null;
        }

        return Carbon::createFromTimestamp(max(array_map('filemtime', $archivos)), config('app.timezone'));
    }

    public static function proximo(): Carbon
    {
        return (static::ultimo() ?? now())->copy()->addDays(static::CADA_DIAS);
    }

    public static function toca(): bool
    {
        $ultimo = static::ultimo();

        return $ultimo === null || $ultimo->lte(now()->subDays(static::CADA_DIAS));
    }

    public static function generarSiToca(): void
    {
        // Cache::add sirve de candado: si dos administradores entran a la vez, solo uno lo genera,
        // y si falla no se vuelve a intentar hasta dentro de una hora
        if (! static::toca() || ! Cache::add('respaldo-automatico-en-curso', true, now()->addHour())) {
            return;
        }

        app()->terminating(function () {
            if (Artisan::call('db:backup', ['--auto' => true]) !== 0) {
                Log::error('Respaldo automático falló: ' . trim(Artisan::output()));

                return;
            }

            Cache::forget('respaldo-automatico-en-curso');
        });
    }
}
