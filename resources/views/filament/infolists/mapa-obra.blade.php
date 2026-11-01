{{-- Mapa de solo lectura con la ubicación de la obra (panel de operadores) --}}
@php
    /** @var \App\Models\Contrato $record */
    $record = $getRecord();
    $color = match ($record->viabilidad) {
        'viable' => '#16a34a',
        'observaciones' => '#d97706',
        'no_viable' => '#dc2626',
        default => '#ea580c',
    };
@endphp

@if ($record->tieneUbicacion())
    <div class="space-y-3">
        <div
            wire:ignore
            x-data="mapaObra({ lat: {{ $record->latitud }}, lng: {{ $record->longitud }}, color: @js($color) })"
        >
            <div x-ref="mapa" class="w-full overflow-hidden rounded-lg ring-1 ring-gray-950/10 dark:ring-white/10" style="height: 20rem; z-index: 0"></div>
        </div>

        <x-filament::button
            tag="a"
            :href="$record->urlComoLlegar()"
            target="_blank"
            icon="heroicon-o-arrow-top-right-on-square"
            size="lg"
            class="w-full"
        >
            Cómo llegar (Google Maps)
        </x-filament::button>
    </div>
@else
    <div class="rounded-lg bg-warning-50 p-4 text-sm text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
        Esta obra todavía no tiene la ubicación marcada en el mapa. Consulta la dirección y referencia.
    </div>
@endif
