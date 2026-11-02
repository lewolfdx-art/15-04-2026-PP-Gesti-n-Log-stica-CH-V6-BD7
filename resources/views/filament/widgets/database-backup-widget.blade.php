<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center gap-3 mb-4">
        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Backup de Base de Datos</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Se hace un respaldo automático cada {{ $cadaDias }} días al abrir el Escritorio. También puedes sacarlo cuando quieras.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <!-- Respaldo manual -->
        <button 
            wire:click="backup" 
            wire:loading.attr="disabled"
            class="flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
            </svg>
            <span wire:loading.remove wire:target="backup">Respaldo ahora</span>
            <span wire:loading wire:target="backup">Generando...</span>
        </button>

        <!-- Backup Mensual -->
        <button 
            wire:click="backupMonthly" 
            wire:loading.attr="disabled"
            class="flex items-center justify-center gap-2 px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-all duration-200 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span wire:loading.remove wire:target="backupMonthly">Respaldo mensual</span>
            <span wire:loading wire:target="backupMonthly">Generando...</span>
        </button>
    </div>

    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
            <span class="font-medium inline-flex items-center gap-1">{{ svg('heroicon-o-check-circle', 'h-4 w-4') }} Último respaldo:</span>
            {{ $ultimo ? $ultimo->format('d/m/Y H:i') . ' (' . $ultimo->locale('es')->diffForHumans() . ')' : 'ninguno todavía' }}
            · <span class="font-medium">Próximo automático:</span> {{ $proximo->format('d/m/Y') }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            <span class="font-medium inline-flex items-center gap-1">{{ svg('heroicon-o-folder', 'h-4 w-4') }} Ubicación:</span> 
            storage/app/backups/
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            <span class="font-medium inline-flex items-center gap-1">{{ svg('heroicon-o-clock', 'h-4 w-4') }} Tiempo estimado:</span> 
            El proceso puede tomar algunos segundos dependiendo del tamaño de la base de datos.
        </p>
    </div>
</div>