<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Respaldos de la BD. Corren si el programador de tareas ejecuta "php artisan schedule:run"
// cada minuto (cron en el hosting, Programador de tareas en Windows).
Schedule::command('db:backup')->dailyAt('23:00')->withoutOverlapping();
Schedule::command('db:backup --monthly')->monthlyOn(1, '23:30')->withoutOverlapping();
