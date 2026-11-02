<x-filament-widgets::widget>
    <div class="cm-bienvenida">
        <div class="cm-bienvenida__texto">
            <p class="cm-bienvenida__fecha">{{ $fecha }}</p>
            <h2 class="cm-bienvenida__saludo">{{ $saludo }}{{ $nombre ? ', ' . $nombre : '' }}</h2>
            <p class="cm-bienvenida__resumen">
                @if($vaciadosHoy > 0)
                    Hoy hay <strong>{{ $vaciadosHoy }} {{ $vaciadosHoy === 1 ? 'vaciado' : 'vaciados' }}</strong> en agenda.
                @else
                    Hoy no hay vaciados en agenda.
                @endif
                @if($porRevisar > 0)
                    Tienes <strong>{{ $porRevisar }} {{ $porRevisar === 1 ? 'cotización' : 'cotizaciones' }}</strong> por revisar.
                @endif
            </p>
        </div>
        <div class="cm-bienvenida__accesos">
            @foreach($accesos as [$texto, $icono, $url])
                <a href="{{ $url }}" class="cm-bienvenida__acceso" wire:navigate>
                    {{ svg($icono, 'h-5 w-5') }}
                    <span>{{ $texto }}</span>
                </a>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
