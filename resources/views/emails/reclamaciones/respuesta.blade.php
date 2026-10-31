<x-mail::message>
# Respuesta a su {{ strtolower(\App\Models\LibroReclamacion::TIPOS[$hoja->tipo] ?? $hoja->tipo) }} N.° {{ $hoja->codigo }}

Estimado(a) {{ $hoja->claimant_name }}:

{{ $hoja->response }}

---

**Hoja de reclamación:** {{ $hoja->codigo }} del {{ $hoja->created_at->format('d/m/Y') }}
**Fecha de respuesta:** {{ $hoja->respondido_at?->format('d/m/Y') }}

Atentamente,
{{ config('empresa.razon_social') }}@if(config('empresa.ruc')) · RUC {{ config('empresa.ruc') }}@endif
</x-mail::message>
