{{-- Comprobante de pago al personal. Diseño basado en la plantilla del visor de cotización. --}}
@use('App\Models\PagoPersonal')
@use('App\Models\Trabajador')
@php
    $empresa = config('empresa');
    $t = $pago->trabajador;
    $soles = fn ($n) => 'S/ ' . number_format((float) $n, 2);
    $cargo = Trabajador::cargoTexto($t->tipo_cargo);
    $contacto = \App\Models\Inicio::getActiveContent();
    $color = fn (?string $c, string $defecto) => preg_match('/^#[0-9a-fA-F]{6}$/', (string) $c) ? $c : $defecto;
    $c1 = $color($empresa['color_principal'] ?? null, '#16222e');
    $c2 = $color($empresa['color_acento'] ?? null, '#f5811e');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Comprobante {{ $pago->codigo }} · {{ $t->nombre_completo }}</title>
<style>
:root { --c1: {{ $c1 }}; --c2: {{ $c2 }}; --tint: color-mix(in srgb, var(--c2) 9%, white); --tint2: color-mix(in srgb, var(--c2) 30%, white); --ink: #1b2430; }
* { box-sizing: border-box; }
body { margin: 0; background: #eef0f2; color: var(--ink); font: 12px/1.45 Arial, Helvetica, sans-serif; }
.toolbar { display: flex; justify-content: center; gap: 10px; padding: 14px; }
.toolbar button { border: 0; border-radius: 6px; padding: 9px 18px; font: inherit; font-weight: 700; cursor: pointer; background: var(--c2); color: var(--c1); }
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

.d-slogan { flex: 1; text-align: center; font-weight: 700; font-size: 12px; line-height: 1.3; text-transform: uppercase; padding: 0 42% 0 14px; position: relative; z-index: 1; opacity: .9; }

/* Barra: contacto con íconos a la izquierda, número y datos del documento a la derecha */
.d-bar { display: flex; align-items: stretch; background: var(--c1); border-top: 3px solid var(--c2); min-height: 62px; }
.d-contact { flex: 1; display: flex; align-items: center; flex-wrap: wrap; gap: 6px 0; padding: 8px 10px; color: #fff; font-size: 10.5px; line-height: 1.3; }
.d-contact .ci { display: flex; gap: 7px; align-items: center; padding: 0 10px; border-right: 1px solid rgba(255,255,255,.35); }
.d-contact .ci:last-child { border-right: 0; }
.d-contact .ci b { font-size: 10px; letter-spacing: .03em; display: block; }
.ico { width: 26px; height: 26px; border-radius: 50%; background: var(--c2); display: flex; align-items: center; justify-content: center; color: var(--c1); flex: none; }
.ico svg { width: 15px; height: 15px; fill: currentColor; display: block; }
.d-right { flex: 0 0 52%; display: flex; background: #fff; min-width: 0; }
.d-no { flex: 0 0 36%; background: var(--c2); color: var(--c1); padding: 8px 12px; display: flex; flex-direction: column; justify-content: center; font-weight: 800; font-size: 11px; text-transform: uppercase; line-height: 1.25; }
.d-no b { font-size: 14px; display: block; overflow-wrap: anywhere; }
.d-fields { flex: 1; min-width: 0; display: grid; grid-template-columns: auto minmax(0, 1fr); gap: 3px 10px; align-content: center; padding: 8px 12px; font-size: 10.5px; }
.d-fields .k { font-weight: 700; text-transform: uppercase; white-space: nowrap; }
.d-fields .v { color: var(--c1); font-weight: 700; overflow-wrap: anywhere; }

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
    .d-bar { flex-direction: column; }
    .d-right { flex-basis: auto; }
    .d-slogan { display: none; }
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
    <button onclick="window.print()"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" style="vertical-align:-3px;margin-right:6px" aria-hidden="true"><path d="M7 3h10v4H7V3zm-2 6h14a2 2 0 0 1 2 2v6h-4v4H7v-4H3v-6a2 2 0 0 1 2-2zm4 7v3h6v-3H9zm9-4a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/></svg>Imprimir / Guardar PDF</button>
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
        <div class="d-slogan">{{ $empresa['eslogan'] }}</div>
        <div class="d-title">
            <div class="t">COMPROBANTE<br>DE PAGO</div>
        </div>
    </div>

    <div class="d-bar">
        <div class="d-contact">
            @if ($contacto?->oficina_telefono)
                <div class="ci">
                    <span class="ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.5.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.5.1.4 0 .8-.2 1l-2.3 2.3z"/></svg></span>
                    <span><b>TELÉFONO</b>{{ $contacto->oficina_telefono }}@if($contacto->oficina_celular)<br>{{ $contacto->oficina_celular }}@endif</span>
                </div>
            @endif
            @if ($contacto?->oficina_email)
                <div class="ci">
                    <span class="ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6.5A1.5 1.5 0 0 1 4.5 5h15A1.5 1.5 0 0 1 21 6.5v11A1.5 1.5 0 0 1 19.5 19h-15A1.5 1.5 0 0 1 3 17.5v-11zm2.2.5 6.8 5.1L18.8 7H5.2zM19 8.6l-6.4 4.8a1 1 0 0 1-1.2 0L5 8.6v8.4h14V8.6z"/></svg></span>
                    <span><b>CORREO</b>{{ $contacto->oficina_email }}</span>
                </div>
            @endif
            @if ($empresa['direccion'] ?? $contacto?->oficina_direccion)
                <div class="ci">
                    <span class="ico"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/></svg></span>
                    <span><b>DIRECCIÓN</b>{{ $empresa['direccion'] ?? $contacto->oficina_direccion }}</span>
                </div>
            @endif
        </div>
        <div class="d-right">
            <div class="d-no">Comprobante N.°<b>{{ $pago->codigo }}</b></div>
            <div class="d-fields">
                <span class="k">Emisión</span><span class="v">{{ $pago->created_at->format('d/m/Y') }}</span>
                <span class="k">Tipo</span><span class="v">{{ PagoPersonal::TIPOS[$pago->tipo] ?? $pago->tipo }}</span>
                <span class="k">Período</span><span class="v">{{ ($pago->quincena === 1 ? '1ra' : '2da') . ' quincena · ' . $pago->periodo_desde->format('d/m') . ' – ' . $pago->periodo_hasta->format('d/m/Y') }}</span>
            </div>
        </div>
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
