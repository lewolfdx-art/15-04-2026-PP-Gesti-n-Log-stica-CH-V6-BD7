<?php

/*
 * Datos legales del proveedor que deben figurar en la Hoja de Reclamación
 * (Código de Protección y Defensa del Consumidor, Ley 29571).
 */
return [
    'razon_social' => env('EMPRESA_RAZON_SOCIAL', 'Concretera Mantaro'),
    'ruc'          => env('EMPRESA_RUC'),
    'direccion'    => env('EMPRESA_DIRECCION'),
    'email'        => env('EMPRESA_EMAIL_RECLAMOS', env('MAIL_FROM_ADDRESS')),

    // Colores de los comprobantes impresos (la plantilla original usa #0A3D91 y #FFC20E)
    'color_principal' => env('EMPRESA_COLOR_PRINCIPAL', '#16222e'),
    'color_acento'    => env('EMPRESA_COLOR_ACENTO', '#f5811e'),
    'eslogan'         => env('EMPRESA_ESLOGAN', 'Concreto premezclado de alta calidad'),

    // Plazo legal de respuesta, en días hábiles
    'plazo_respuesta_dias' => (int) env('RECLAMOS_PLAZO_DIAS', 15),
];
