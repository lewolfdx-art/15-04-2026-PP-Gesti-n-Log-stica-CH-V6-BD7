<x-filament-widgets::widget>
    <div class="cm-pagos">
        <div class="cm-pagos__cifras">
            <div>
                <span class="cm-pagos__etiqueta">Total del contrato</span>
                <span class="cm-pagos__monto">S/ {{ number_format($total, 2) }}</span>
            </div>
            <div>
                <span class="cm-pagos__etiqueta">Pagado</span>
                <span class="cm-pagos__monto cm-pagos__monto--pagado">S/ {{ number_format($pagado, 2) }}</span>
            </div>
            <div>
                <span class="cm-pagos__etiqueta">Saldo por cobrar</span>
                <span class="cm-pagos__monto {{ $saldo > 0 ? 'cm-pagos__monto--debe' : 'cm-pagos__monto--pagado' }}">
                    {{ $saldo > 0 ? 'S/ ' . number_format($saldo, 2) : 'Pagado completo' }}
                </span>
            </div>
        </div>

        <div class="cm-pagos__barra" title="{{ $avance }} % pagado">
            <span style="width: {{ $avance }}%"></span>
        </div>

        @if($sinNumero)
            <p class="cm-pagos__aviso">Este contrato no tiene N.° de contrato: asígnale uno para poder registrar pagos.</p>
        @elseif($pagos->isEmpty())
            <p class="cm-pagos__vacio">Todavía no hay pagos registrados.</p>
        @else
            <ul class="cm-pagos__lista">
                @foreach($pagos as $pago)
                    <li>
                        <span>{{ $pago->fecha?->format('d/m/Y') }}</span>
                        <span class="cm-pagos__tipo">{{ $pago->modo_pago === 'CAN-CONTRATO' ? 'Cancelación' : ($pago->modo_pago === 'ADEL-CONTRATO' ? 'Adelanto / abono' : 'Otro') }}</span>
                        <span class="cm-pagos__detalle">{{ $pago->obs ?: $pago->detalle }}</span>
                        <strong>S/ {{ number_format((float) $pago->monto, 2) }}</strong>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-filament-widgets::widget>
