<x-filament-widgets::widget>
    <div class="cm-respaldo">
        <div class="cm-respaldo__icono">
            {{ svg('heroicon-o-circle-stack', 'h-6 w-6') }}
        </div>

        <div class="cm-respaldo__texto">
            <h3>Respaldo de la base de datos</h3>
            <p>
                <span>Último: <strong>{{ $ultimo ? $ultimo->format('d/m/Y H:i') . ' (' . $ultimo->locale('es')->diffForHumans() . ')' : 'ninguno todavía' }}</strong></span>
                <span>Próximo automático: <strong>{{ $proximo->format('d/m/Y') }}</strong></span>
                <span>Cada {{ $cadaDias }} días, al abrir el Escritorio · storage/app/backups</span>
            </p>
        </div>

        <div class="cm-respaldo__botones">
            <button type="button" wire:click="backup" wire:loading.attr="disabled" class="cm-respaldo__btn cm-respaldo__btn--principal">
                {{ svg('heroicon-o-arrow-down-tray', 'h-5 w-5') }}
                <span wire:loading.remove wire:target="backup">Respaldo ahora</span>
                <span wire:loading wire:target="backup">Generando...</span>
            </button>
            <button type="button" wire:click="backupMonthly" wire:loading.attr="disabled" class="cm-respaldo__btn">
                {{ svg('heroicon-o-calendar', 'h-5 w-5') }}
                <span wire:loading.remove wire:target="backupMonthly">Respaldo mensual</span>
                <span wire:loading wire:target="backupMonthly">Generando...</span>
            </button>
        </div>
    </div>
</x-filament-widgets::widget>
