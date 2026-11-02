<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Los respaldos automáticos (cada 14 días) no usan el programador de tareas:
// los dispara el Escritorio, ver App\Support\RespaldoAutomatico.
