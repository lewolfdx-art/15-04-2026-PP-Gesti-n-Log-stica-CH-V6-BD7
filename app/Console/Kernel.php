<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Backups existentes
        $schedule->command('db:backup')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/backup.log'));
    
        $schedule->command('db:backup --monthly')
            ->lastDayOfMonth('03:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/backup.log'));
        
        // 👇 NUEVO: Verificación de notificaciones de cuentas por pagar
        $schedule->command('payables:check-notifications')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/notifications.log'));
        
        $schedule->command('payables:check-notifications')
            ->dailyAt('18:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/notifications.log'));
        
        // Opcional: cada 6 horas durante el día
        $schedule->command('payables:check-notifications')
            ->everySixHours()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/notifications.log'));
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}