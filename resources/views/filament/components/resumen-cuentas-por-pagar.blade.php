<div class="mb-6 space-y-6">

    <!-- TOTAL GENERAL POR PAGAR -->
    <div class="bg-gray-900 dark:bg-gray-800 border border-gray-700 rounded-2xl p-6">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-xl font-bold text-white">TOTAL GENERAL POR PAGAR</h3>
                <p class="text-gray-400 text-sm mt-1">Cuentas pendientes de pago</p>
            </div>
            <div class="text-right">
                <span class="text-4xl font-bold text-white">
                    S/ {{ number_format($total_por_pagar ?? 0, 2) }}
                </span>
            </div>
        </div>
    </div>

    <!-- HISTORIAL DE PAGOS REALIZADOS -->
    @if(isset($historial) && $historial->isNotEmpty())
    <div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <span>◎</span>
            Historial de Pagos Realizados
        </h3>

        <div class="space-y-3">
            @foreach($historial as $item)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">
                            {{ $item->detalle }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            @php
                                if ($item->tipo === 'fin_mes') {
                                    echo 'Fin de Mes - ' . ucfirst($item->mes ?? '') . ' ' . ($item->año ?? '');
                                } elseif ($item->tipo === 'mes') {
                                    echo ucfirst($item->mes ?? '') . ' ' . ($item->año ?? '');
                                } elseif ($item->fecha) {
                                    echo $item->fecha->format('d/m/Y');
                                } else {
                                    echo ucfirst($item->tipo ?? 'N/A');
                                }
                            @endphp
                        </div>
                    </div>

                    <div class="text-right">
                        <div class="font-bold text-emerald-600 dark:text-emerald-400 text-xl">
                            S/ {{ number_format($item->total ?? 0, 2) }}
                        </div>
                        <div class="text-xs text-emerald-500 mt-1">Pagado</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-gray-800 border border-gray-700 rounded-2xl p-8 text-center">
        <p class="text-gray-400">No hay pagos realizados todavía</p>
    </div>
    @endif

</div>