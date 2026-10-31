{{-- Comprobante de pago al personal. Diseño basado en la plantilla del visor de cotización. --}}
@use('App\Models\PagoPersonal')
@use('App\Models\Trabajador')
@php
    $empresa = config('empresa');
    $t = $pago->trabajador;
    $soles = fn ($n) => 'S/ ' . number_format((float) $n, 2);
    $cargo = trim(preg_replace('/^\X\s*/u', '', Trabajador::cargoTexto($t->tipo_cargo)));
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Comprobante {{ $pago->codigo }} · {{ $t->nombre_completo }}</title>
<style>
:root { --c1: #16222e; --c2: #f5811e; --tint: #fff4ea; --tint2: #fbd9bb; --ink: #1b2430; }
* { box-sizing: border-box; }
body { margin: 0; background: #eef0f2; color: var(--ink); font: 12px/1.45 Arial, Helvetica, sans-serif; }
.toolbar { display: flex; justify-content: center; gap: 10px; padding: 14px; }
.toolbar button { border: 0; border-radius: 6px; padding: 9px 18px; font: inherit; font-weight: 700; cursor: pointer; background: var(--c2); color: #fff; }
.toolbar button.sec { background: #fff; color: var(--c1); border: 1px solid #cfd5da; }

.paper { background: #fff; max-width: 820px; margin: 0 auto 30px; box-shadow: 0 2px 14px rgba(0,0,0,.18); overflow: hidden; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

/* Encabezado: marca a la izquierda, título en bloque diagonal a la derecha */
.d-head { position: relative; background: var(--c1); color: #fff; display: flex; align-items: center; min-height: 112px; padding: 14px 18px; overflow: hidden; }
.d-logo { display: flex; align-items: center; gap: 12px; position: relative; z-index: 1; max-width: 58%; }
.d-logo img { height: 60px; width: auto; background: #fff; border-radius: 8px; padding: 4px; }
.d-logo .nm { font-size: 19px; font-weight: 900; line-height: 1.1; }
.d-logo .ruc { font-size: 10px; opacity: .85; margin-top: 4px; letter-spacing: .04em; }
.d-title { position: absolute; right: 0; top: 0; bottom: 0; width: 40%; background: var(--c2); clip-path: polygon(17% 0, 100% 0, 100% 100%, 0 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding-left: 12%; color: var(--c1); font-weight: 900; text-align: center; }
.d-title .t { font-size: 21px; line-height: 1.1; }
.d-title .n { font-size: 13px; margin-top: 6px; }

.d-bar { display: flex; flex-wrap: wrap; gap: 4px 28px; background: var(--c1); border-top: 3px solid var(--c2); color: #fff; padding: 9px 18px; font-size: 11px; }
.d-bar b { color: var(--c2); }

.d-body { padding: 16px; }

/* Recuadro con pestaña */
.d-box { position: relative; background: var(--tint); border-radius: 8px; padding: 30px 14px 10px; margin-bottom: 14px; }
.d-box .tab { position: absolute; left: 0; top: 0; background: var(--c1); color: #fff; font-weight: 800; padding: 4px 30px 4px 12px; clip-path: polygon(0 0, 100% 0, 88% 100%, 0 100%); border-top-left-radius: 8px; font-size: 12px; }
.d-box .g { display: grid; grid-template-columns: 1fr 1fr; gap: 0 26px; }
.r2 { display: grid; grid-template-columns: 96px 1fr; border-bottom: 1px solid var(--tint2); padding: 3px 0; }
.r2 .k { color: #4b5563; }
.r2 .v { font-weight: 700; }

table.d-items { width: 100%; border-collapse: collapse; border: 2px solid var(--c1); }
.d-items th { background: var(--c1); color: #fff; font-size: 11px; padding: 8px 6px; text-align: left; }
.d-items td { padding: 7px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
.d-items td.r, .d-items th.r { text-align: right; white-space: nowrap; }
.d-items tr.sub td { background: #f9fafb; font-weight: 700; }
.badge { display: inline-block; padding: 1px 7px; border-radius: 10px; font-size: 10px; font-weight: 700; }
.badge.ok { background: #dcfce7; color: #166534; }
.badge.no { background: #fee2e2; color: #991b1b; }

.d-totals { width: 300px; margin: 10px 0 0 auto; font-size: 12px; }
.d-totals .tr { display: flex; justify-content: space-between; padding: 3px 8px; }
.d-totals .tr small { color: #6b7280; display: block; }
.d-grand { display: flex; margin-top: 6px; font-weight: 900; }
.d-grand .a { background: var(--c2); color: var(--c1); padding: 9px 30px 9px 12px; clip-path: polygon(0 0, 100% 0, 88% 100%, 0 100%); font-size: 14px; position: relative; z-index: 1; }
.d-grand .b { flex: 1; background: var(--c1); color: #fff; text-align: right; padding: 9px 12px 9px 24px; margin-left: -18px; font-size: 16px; }

.d-obs { margin-top: 14px; font-size: 11px; color: #4b5563; }

.d-firmas { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; margin: 60px 30px 10px; text-align: center; font-size: 11px; }
.d-firmas div { border-top: 1px solid #1b2430; padding-top: 6px; }

.d-foot { display: flex; justify-content: space-between; align-items: stretch; margin-top: 16px; }
.d-foot .l { padding: 0 16px 14px; align-self: flex-end; font-size: 10px; color: #6b7280; }
.d-foot .r { width: 46%; background: linear-gradient(110deg, transparent 0 10%, var(--c2) 10% 16%, var(--c1) 16% 100%); color: #fff; font-weight: 800; text-align: center; padding: 14px 10px 14px 60px; text-transform: uppercase; font-size: 12px; }

@media (max-width: 640px) {
    .d-box .g { grid-template-columns: 1fr; }
    .d-title { width: 44%; }
    .d-title .t { font-size: 16px; }
    .d-logo .nm { font-size: 15px; }
}
@media print {
    body { background: #fff; }
    .toolbar { display: none !important; }
    .paper { box-shadow: none; margin: 0; max-width: none; }
    @page { margin: 10mm; }
}
</style>
</head>
<body>
<div class="toolbar">
    <button onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>
    <button class="sec" onclick="window.close()">Cerrar</button>
</div>

<div class="paper">
    <div class="d-head">
        <div class="d-logo">
            <img src="{{ asset('images/logo.png') }}" alt="">
            <div>
                <div class="nm">{{ $empresa['razon_social'] }}</div>
                @if ($empresa['ruc'])
                    <div class="ruc">RUC {{ $empresa['ruc'] }}</div>
                @endif
            </div>
        </div>
        <div class="d-title">
            <div class="t">COMPROBANTE<br>DE PAGO</div>
            <div class="n">N.° {{ $pago->codigo }}</div>
        </div>
    </div>

    <div class="d-bar">
        <span><b>Tipo:</b> {{ PagoPersonal::TIPOS[$pago->tipo] ?? $pago->tipo }}</span>
        <span><b>Período:</b> {{ $pago->periodoTexto() }}</span>
        <span><b>Emitido:</b> {{ $pago->created_at->format('d/m/Y') }}</span>
    </div>

    <div class="d-body">
        <div class="d-box">
            <span class="tab">TRABAJADOR</span>
            <div class="g">
                <div>
                    <div class="r2"><span class="k">Nombre</span><span class="v">{{ $t->nombre_completo }}</span></div>
                    <div class="r2"><span class="k">DNI</span><span class="v">{{ $t->dni ?: '—' }}</span></div>
                </div>
                <div>
                    <div class="r2"><span class="k">Cargo</span><span class="v">{{ $cargo }}</span></div>
                    <div class="r2"><span class="k">Forma de pago</span><span class="v">{{ PagoPersonal::FORMAS_PAGO[$pago->forma_pago] ?? '—' }}</span></div>
                </div>
            </div>
        </div>

        <table class="d-items">
            @if ($pago->tipo === 'comision')
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>N.° contrato</th>
                        <th>Cliente</th>
                        <th>Pago del cliente</th>
                        <th class="r">Comisión</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pago->contratos as $c)
                        <tr>
                            <td>{{ $c->fecha?->format('d/m/Y') }}</td>
                            <td>{{ $c->numero_contrato ?: '—' }}</td>
                            <td>{{ $c->nombre_cliente }}</td>
                            <td><span class="badge {{ $c->estado_pago === 'PAGADO' ? 'ok' : 'no' }}">{{ $c->estado_pago }}</span></td>
                            <td class="r">{{ $soles($c->comision) }}</td>
                        </tr>
                    @endforeach
                    <tr class="sub">
                        <td colspan="4">Total de comisiones ({{ $pago->contratos->count() }} contrato{{ $pago->contratos->count() === 1 ? '' : 's' }})</td>
                        <td class="r">{{ $soles($pago->monto_base) }}</td>
                    </tr>
                </tbody>
            @else
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th class="r">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sueldo · {{ $pago->periodoTexto() }}</td>
                        <td class="r">{{ $soles($pago->monto_base) }}</td>
                    </tr>
                </tbody>
            @endif
        </table>

        <div class="d-totals">
            <div class="tr"><span>{{ $pago->tipo === 'comision' ? 'Comisiones' : 'Sueldo' }}</span><span>{{ $soles($pago->monto_base) }}</span></div>
            @if ((float) $pago->adicionales > 0)
                <div class="tr"><span>(+) Adicionales @if($pago->concepto_adicionales)<small>{{ $pago->concepto_adicionales }}</small>@endif</span><span>{{ $soles($pago->adicionales) }}</span></div>
            @endif
            @if ((float) $pago->descuentos > 0)
                <div class="tr"><span>(−) Descuentos @if($pago->concepto_descuentos)<small>{{ $pago->concepto_descuentos }}</small>@endif</span><span>−{{ $soles($pago->descuentos) }}</span></div>
            @endif
            <div class="d-grand"><span class="a">TOTAL PAGADO</span><span class="b">{{ $soles($pago->total) }}</span></div>
        </div>

        @if ($pago->observacion)
            <div class="d-obs"><b>Observación:</b> {{ $pago->observacion }}</div>
        @endif

        <div class="d-firmas">
            <div>Recibí conforme<br><b>{{ $t->nombre_completo }}</b><br>DNI {{ $t->dni ?: '________' }}</div>
            <div>Entregado por<br><b>{{ $pago->registradoPor?->name ?? $empresa['razon_social'] }}</b><br>{{ $pago->pagado_at?->format('d/m/Y') ?? 'Pendiente de entrega' }}</div>
        </div>
    </div>

    <div class="d-foot">
        <div class="l">{{ $empresa['razon_social'] }}@if($empresa['direccion']) · {{ $empresa['direccion'] }}@endif</div>
        <div class="r">Tu proyecto, nuestro legado</div>
    </div>
</div>
</body>
</html>
