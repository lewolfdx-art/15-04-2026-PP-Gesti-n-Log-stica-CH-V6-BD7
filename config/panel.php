<?php

/*
 * Ajustes de los paneles /admin y /operador.
 */
return [
    // Cada cuánto la campanita revisa si hay notificaciones nuevas (Filament: '10s', '30s', '1m'...).
    // En la PC con "php artisan serve" conviene 10s o más; en un hosting se puede bajar a 5s.
    'notificaciones_cada' => env('NOTIFICACIONES_CADA', '10s'),
];
