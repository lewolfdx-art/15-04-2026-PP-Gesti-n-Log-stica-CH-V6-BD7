<div class="mb-6 space-y-4">
    <!-- Resumen Semanal -->
    @if(!empty($totales['semanal']))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Resumen Semanal</h3>
        <div class="max-h-48 overflow-y-auto space-y-2" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">
            @foreach($totales['semanal'] as $periodo => $monto)
            @php
                // Intentar extraer o formatear el período con año si es necesario
                // Si $periodo ya tiene año, mostrarlo; si no, se puede dejar igual
                $periodoMostrar = $periodo;
            @endphp
            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                <span class="text-gray-600 dark:text-gray-400">{{ $periodoMostrar }}</span>
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
        <div class="max-h-48 overflow-y-auto space-y-2" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">
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
        <div class="max-h-48 overflow-y-auto space-y-2" style="scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">
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

    <!-- ========== HISTORIAL DE PAGOS REALIZADOS (EN FILAS, CON SCROLL VERTICAL) ========== -->
    @php
        // Agrupar el historial por período
        $historialAgrupado = [
            'semanal' => [],
            'quincenal' => [],
            'fin_mes' => [],
        ];
        
        if(isset($historial) && count($historial) > 0) {
            foreach($historial as $item) {
                if ($item->tipo_periodo === 'semanal') {
                    $key = "Semana {$item->semana}";
                    if ($item->fecha_desde && $item->fecha_hasta) {
                        $key .= " (" . \Carbon\Carbon::parse($item->fecha_desde)->format('d/m') . 
                        " - " . \Carbon\Carbon::parse($item->fecha_hasta)->format('d/m') . 
                        "/" . \Carbon\Carbon::parse($item->fecha_desde)->format('Y') . ")";
                    }
                    if (!isset($historialAgrupado['semanal'][$key])) {
                        $historialAgrupado['semanal'][$key] = 0;
                    }
                    $historialAgrupado['semanal'][$key] += $item->monto;
                } elseif ($item->tipo_periodo === 'quincena') {
                    $key = $item->quincena === 'PRIMERA' ? 'Primera Quincena' : 'Segunda Quincena';
                    $key .= " ({$item->mes}/{$item->año})";
                    if (!isset($historialAgrupado['quincenal'][$key])) {
                        $historialAgrupado['quincenal'][$key] = 0;
                    }
                    $historialAgrupado['quincenal'][$key] += $item->monto;
                } elseif ($item->tipo_periodo === 'fin_mes') {
                    $key = "Fin de Mes - {$item->mes}/{$item->año}";
                    if (!isset($historialAgrupado['fin_mes'][$key])) {
                        $historialAgrupado['fin_mes'][$key] = 0;
                    }
                    $historialAgrupado['fin_mes'][$key] += $item->monto;
                }
            }
        }
        
        $totalGrupos = count($historialAgrupado['semanal']) + count($historialAgrupado['quincenal']) + count($historialAgrupado['fin_mes']);
    @endphp

    @if($totalGrupos > 0)
    <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Historial de Pagos Realizados
        </h3>
        
        <!-- Contenedor con scroll vertical si hay más de 6 filas -->
        <div class="max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg" 
             style="scrollbar-width: thin; scrollbar-color: #cbd5e1 #f1f5f9;">
            
            <!-- Pagados Semanales -->
            @if(!empty($historialAgrupado['semanal']))
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">📅 Pagados - Semanal</h4>
                <div class="space-y-2">
                    @foreach($historialAgrupado['semanal'] as $periodo => $monto)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">{{ $periodo }}</span>
                        <span class="font-bold text-green-600 dark:text-green-400">S/ {{ number_format($monto, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Pagados Quincenales -->
            @if(!empty($historialAgrupado['quincenal']))
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">📅 Pagados - Quincenal</h4>
                <div class="space-y-2">
                    @foreach($historialAgrupado['quincenal'] as $periodo => $monto)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">{{ $periodo }}</span>
                        <span class="font-bold text-green-600 dark:text-green-400">S/ {{ number_format($monto, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Pagados Fin de Mes -->
            @if(!empty($historialAgrupado['fin_mes']))
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border-t border-gray-200 dark:border-gray-700">
                <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-2">📅 Pagados - Fin de Mes</h4>
                <div class="space-y-2">
                    @foreach($historialAgrupado['fin_mes'] as $periodo => $monto)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">{{ $periodo }}</span>
                        <span class="font-bold text-green-600 dark:text-green-400">S/ {{ number_format($monto, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <style>
            .overflow-y-auto::-webkit-scrollbar {
                width: 6px;
            }
            .overflow-y-auto::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 10px;
            }
            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    </div>
    @else
    <div class="mt-8 bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-center">
        <p class="text-gray-500 dark:text-gray-400 text-sm">No hay pagos registrados aún</p>
    </div>
    @endif
</div>