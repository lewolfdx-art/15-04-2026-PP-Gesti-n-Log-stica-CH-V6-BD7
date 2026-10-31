<x-mail::message>
# Hoja de reclamación N.° {{ $hoja->codigo }}

Estimado(a) {{ $hoja->claimant_name }}, hemos registrado su **{{ strtolower(\App\Models\LibroReclamacion::TIPOS[$hoja->tipo] ?? $hoja->tipo) }}**. Esta es su copia.

**Proveedor:** {{ config('empresa.razon_social') }}@if(config('empresa.ruc')) · RUC {{ config('empresa.ruc') }}@endif
@if(config('empresa.direccion'))
**Dirección:** {{ config('empresa.direccion') }}
@endif
**Fecha:** {{ $hoja->created_at->format('d/m/Y H:i') }}

## 1. Consumidor
**Nombre:** {{ $hoja->claimant_name }}
**Documento:** {{ $hoja->document_type }} {{ $hoja->document_number }}
**Domicilio:** {{ $hoja->address ?: '—' }}
**Teléfono:** {{ $hoja->phone }} · **Correo:** {{ $hoja->email }}
@if($hoja->menor_edad)
**Padre, madre o apoderado:** {{ $hoja->apoderado_nombre }}
@endif

## 2. Bien contratado
**Tipo:** {{ \App\Models\LibroReclamacion::BIENES[$hoja->bien_tipo] ?? '—' }}
**Monto reclamado:** {{ $hoja->monto !== null ? 'S/ ' . number_format((float) $hoja->monto, 2) : '—' }}
**Descripción:** {{ $hoja->bien_descripcion }}
@if($hoja->order_number)
**N.° de contrato / pedido:** {{ $hoja->order_number }}
@endif

## 3. Detalle
**Tipo:** {{ \App\Models\LibroReclamacion::TIPOS[$hoja->tipo] ?? $hoja->tipo }}

**Detalle:** {{ $hoja->claim_description }}

**Pedido del consumidor:** {{ $hoja->petition ?: '—' }}

---

Le responderemos en un plazo no mayor a **{{ config('empresa.plazo_respuesta_dias') }} días hábiles**, a más tardar el **{{ $hoja->fecha_limite->format('d/m/Y') }}**.

<small>La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI.</small>

{{ config('empresa.razon_social') }}
</x-mail::message>
