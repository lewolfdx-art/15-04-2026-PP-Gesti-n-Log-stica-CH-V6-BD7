<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inicio extends Model
{
    protected $table = 'inicios';
    
    protected $fillable = [
        'hero_title', 'hero_description', 'hero_image',
        'beneficios', 'proyectos_recientes', 'nuestras_operaciones', 'cta_text', 
        'cta_url', 'is_active',
        // Logo
        'logo_image', 'logo_text', 'logo_text_color', 'logo_span_color',
        'favicon', // Agregado
        // Footer
        'oficina_titulo', 'oficina_direccion', 'oficina_telefono',
        'oficina_celular', 'oficina_email', 'oficina_maps_url',
        'planta_titulo', 'planta_ubicacion', 'planta_maps_url', 'planta_whatsapp',
        'atencion_titulo', 'atencion_horario_lunes_sabado',
        'atencion_horario_domingo', 'atencion_asesora_comercial',
        'social_networks', 'whatsapp_number',
        // Portada rediseñada
        'cifras', 'zonas_cobertura',
    ];
    
    protected $casts = [
        'beneficios' => 'array',
        'proyectos_destacados' => 'array',
        'proyectos_recientes' => 'array',
        'nuestras_operaciones' => 'array',
        'social_networks' => 'array',
        'cifras' => 'array',
        'zonas_cobertura' => 'array',
        'is_active' => 'boolean',
    ];
    
    public static function getActiveContent()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * wa.me necesita solo dígitos con código de país: "957 924 586" → "51957924586"
     */
    public static function normalizarWhatsapp(?string $numero): ?string
    {
        $digitos = preg_replace('/\D/', '', (string) $numero);

        if ($digitos === '') {
            return null;
        }

        // Celular peruano de 9 dígitos sin código de país
        return strlen($digitos) === 9 ? '51' . $digitos : $digitos;
    }

    /**
     * Obras de la portada (Proyectos recientes) y de Experiencia (Sostenibilidad) en una sola lista.
     * Si la misma obra está en las dos, se queda la versión con foto. Primero las que tienen foto.
     */
    public static function obrasRealizadas(): \Illuminate\Support\Collection
    {
        return collect(static::getActiveContent()?->proyectos_recientes ?? [])
            ->merge(Sostenibilidad::getActiveContent()?->proyectos_realizados ?? [])
            ->filter(fn ($obra) => filled($obra['title'] ?? null))
            ->groupBy(fn ($obra) => mb_strtolower(trim($obra['title'])))
            ->map(fn ($iguales) => $iguales->first(fn ($obra) => ! empty($obra['image'])) ?? $iguales->first())
            ->sortBy(fn ($obra) => empty($obra['image']) ? 1 : 0)
            ->values();
    }

    public static function enlaceWhatsapp(?string $numero, ?string $mensaje = null): ?string
    {
        $numero = static::normalizarWhatsapp($numero);

        if (! $numero) {
            return null;
        }

        return "https://wa.me/{$numero}" . ($mensaje ? '?text=' . rawurlencode($mensaje) : '');
    }
}