{{-- Selector de ubicación de la obra: escribe en data.latitud / data.longitud del formulario --}}
<div
    wire:ignore
    x-data="mapaObra({
        lat: $wire.$entangle('data.latitud'),
        lng: $wire.$entangle('data.longitud'),
        editable: true,
    })"
    class="space-y-2"
>
    <div class="flex flex-wrap items-center gap-2 text-sm">
        <span class="text-gray-600 dark:text-gray-300">
            Haz clic en el mapa para marcar la obra. Puedes arrastrar el marcador para ajustarlo.
        </span>
        <x-filament::button size="xs" color="gray" icon="heroicon-o-map-pin" x-on:click="usarMiUbicacion()" x-bind:disabled="buscandoUbicacion">
            <span x-text="buscandoUbicacion ? 'Buscando…' : 'Usar mi ubicación'"></span>
        </x-filament::button>
        <x-filament::button size="xs" color="danger" outlined icon="heroicon-o-x-mark" x-on:click="quitar()" x-show="tieneUbicacion()">
            Quitar marca
        </x-filament::button>
    </div>

    <p x-show="errorUbicacion" x-text="errorUbicacion" class="text-sm text-danger-600"></p>

    <div x-ref="mapa" class="w-full overflow-hidden rounded-lg ring-1 ring-gray-950/10 dark:ring-white/10" style="height: 24rem; z-index: 0"></div>

    <p class="text-xs text-gray-500 dark:text-gray-400">
        <span x-show="tieneUbicacion()">📍 <span x-text="`${lat}, ${lng}`"></span></span>
        <span x-show="! tieneUbicacion()">Ubicación: sin marcar</span>
    </p>
</div>
