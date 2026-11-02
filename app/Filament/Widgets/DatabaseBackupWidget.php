<?php

namespace App\Filament\Widgets;

use App\Support\RespaldoAutomatico;
use Exception;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

class DatabaseBackupWidget extends Widget
{
    protected string $view = 'filament.widgets.database-backup-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -1;

    public static function canView(): bool
    {
        return auth()->user() && auth()->user()->hasRole('super_admin');
    }

    protected function getViewData(): array
    {
        $ultimo = RespaldoAutomatico::ultimo();

        return [
            'ultimo'   => $ultimo,
            'proximo'  => RespaldoAutomatico::proximo(),
            'cadaDias' => RespaldoAutomatico::CADA_DIAS,
        ];
    }

    public function backup()
    {
        try {
            if (Artisan::call('db:backup') !== 0) {
                throw new Exception(trim(Artisan::output()));
            }

            Notification::make()
                ->title('Respaldo completado')
                ->body('El respaldo de la base de datos se generó correctamente.')
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Error al generar backup')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function backupMonthly()
    {
        try {
            if (Artisan::call('db:backup --monthly') !== 0) {
                throw new Exception(trim(Artisan::output()));
            }

            Notification::make()
                ->title('Backup mensual completado')
                ->body('El backup mensual de la base de datos se ha generado correctamente.')
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title('Error al generar backup mensual')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}