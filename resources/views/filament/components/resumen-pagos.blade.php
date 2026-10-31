{{--
    Resumen de pagos compartido por Cuentas por Pagar y Reporte Semanal.
    Variables: total_pendiente, cantidad_pendientes, pagado_mes, desglose_titulo,
    desglose [etiqueta => monto], columna_detalle, historial (fecha, titulo, subtitulo, periodo, monto).
--}}
@php
    $soles = fn ($monto) => 'S/ ' . number_format((float) $monto, 2);
    $maxDesglose = max(array_values($desglose) ?: [0]);
    $mesActual = ucfirst(now()->locale('es')->monthName);
@endphp

<div class="mb-6 space-y-4 px-4 pt-4 sm:px-6">

    {{-- Indicadores --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ svg('heroicon-o-banknotes', 'h-5 w-5 text-danger-500') }}
                Total por pagar
            </div>
            <div class="mt-2 text-3xl font-semibold tracking-tight text-danger-600 dark:text-danger-400">
                {{ $soles($total_pendiente) }}
            </div>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ svg('heroicon-o-check-circle', 'h-5 w-5 text-success-500') }}
                Pagado en {{ $mesActual }}
            </div>
            <div class="mt-2 text-3xl font-semibold tracking-tight text-success-600 dark:text-success-400">
                {{ $soles($pagado_mes) }}
            </div>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ svg('heroicon-o-clock', 'h-5 w-5 text-warning-500') }}
                Pendientes
            </div>
            <div class="mt-2 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                {{ $cantidad_pendientes }}
                <span class="text-base font-normal text-gray-500 dark:text-gray-400">
                    {{ $cantidad_pendientes === 1 ? 'registro' : 'registros' }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- Pendiente desglosado --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white">{{ $desglose_titulo }}</h3>

            @if (empty($desglose))
                <div class="mt-6 flex flex-col items-center gap-2 py-4 text-center">
                    {{ svg('heroicon-o-face-smile', 'h-8 w-8 text-success-500') }}
                    <p class="text-sm text-gray-500 dark:text-gray-400">No hay deudas pendientes</p>
                </div>
            @else
                <ul class="mt-4 space-y-4">
                    @foreach ($desglose as $etiqueta => $monto)
                        <li>
                            <div class="flex items-baseline justify-between gap-2 text-sm">
                                <span class="text-gray-600 dark:text-gray-300">{{ $etiqueta }}</span>
                                <span class="font-semibold tabular-nums text-gray-950 dark:text-white">{{ $soles($monto) }}</span>
                            </div>
                            <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/5">
                                <div class="h-full rounded-full bg-danger-500"
                                     style="width: {{ $maxDesglose > 0 ? max(4, round($monto / $maxDesglose * 100)) : 0 }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Últimos pagos realizados --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 lg:col-span-2 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex items-center justify-between gap-2 px-5 pt-5 pb-3">
                <h3 class="flex items-center gap-2 text-base font-semibold text-gray-950 dark:text-white">
                    {{ svg('heroicon-o-check-badge', 'h-5 w-5 text-success-500') }}
                    Últimos pagos realizados
                </h3>
                @if ($historial->isNotEmpty())
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $historial->count() }} {{ $historial->count() === 1 ? 'pago' : 'pagos' }}
                    </span>
                @endif
            </div>

            @if ($historial->isEmpty())
                <div class="flex flex-col items-center gap-2 px-5 pb-8 pt-4 text-center">
                    {{ svg('heroicon-o-inbox', 'h-8 w-8 text-gray-400') }}
                    <p class="text-sm text-gray-500 dark:text-gray-400">Todavía no hay pagos registrados</p>
                </div>
            @else
                <div class="max-h-80 overflow-y-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="sticky top-0 bg-gray-50 text-xs uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-5 py-2 font-medium">Fecha pago</th>
                                <th class="px-5 py-2 font-medium">{{ $columna_detalle }}</th>
                                <th class="hidden px-5 py-2 font-medium md:table-cell">Período</th>
                                <th class="px-5 py-2 text-right font-medium">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                            @foreach ($historial as $pago)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="whitespace-nowrap px-5 py-3 tabular-nums text-gray-500 dark:text-gray-400">
                                        {{ $pago['fecha']?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="font-medium text-gray-950 dark:text-white">{{ $pago['titulo'] ?: '—' }}</div>
                                        @if (filled($pago['subtitulo']))
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $pago['subtitulo'] }}</div>
                                        @endif
                                        <div class="text-xs text-gray-500 md:hidden dark:text-gray-400">{{ $pago['periodo'] }}</div>
                                    </td>
                                    <td class="hidden px-5 py-3 text-gray-600 md:table-cell dark:text-gray-300">{{ $pago['periodo'] }}</td>
                                    <td class="whitespace-nowrap px-5 py-3 text-right font-semibold tabular-nums text-success-600 dark:text-success-400">
                                        {{ $soles($pago['monto']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
