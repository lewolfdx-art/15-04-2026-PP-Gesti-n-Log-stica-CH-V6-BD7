<div class="mb-6 space-y-4">
    <!-- Resumen Semanal -->
    @if(!empty($totales['semanal']))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Resumen Semanal</h3>
        <div class="space-y-2">
            @foreach($totales['semanal'] as $periodo => $monto)
            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">{{ $periodo }}</span>
                <span class="font-bold text-gray-900 dark:text-white">S/ {{ number_format($monto, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Resumen Quincenal -->
    @if(!empty($totales['quincenal']))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Resumen Quincenal</h3>
        <div class="space-y-2">
            @foreach($totales['quincenal'] as $periodo => $monto)
            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">{{ $periodo }}</span>
                <span class="font-bold text-gray-900 dark:text-white">S/ {{ number_format($monto, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Resumen Fin de Mes -->
    @if(!empty($totales['fin_mes']))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Resumen Fin de Mes</h3>
        <div class="space-y-2">
            @foreach($totales['fin_mes'] as $periodo => $monto)
            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">{{ $periodo }}</span>
                <span class="font-bold text-gray-900 dark:text-white">S/ {{ number_format($monto, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Gran Total -->
    @php
        $granTotal = collect($totales['semanal'])->sum() + 
                     collect($totales['quincenal'])->sum() + 
                     collect($totales['fin_mes'])->sum();
    @endphp
    
    @if($granTotal > 0)
    <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg shadow p-4 border-2 border-primary-200 dark:border-primary-800">
        <div class="flex justify-between items-center">
            <span class="text-lg font-bold text-primary-700 dark:text-primary-300">TOTAL GENERAL POR PAGAR</span>
            <span class="text-2xl font-bold text-primary-700 dark:text-primary-300">S/ {{ number_format($granTotal, 2) }}</span>
        </div>
    </div>
    @endif
</div>