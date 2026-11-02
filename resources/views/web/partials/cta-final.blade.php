{{-- Llamado final antes del pie: cotizar o escribir por WhatsApp --}}
@php
    $config ??= App\Models\Inicio::getActiveContent();
    $telefonoLlamar = $config->oficina_celular ?? $config->oficina_telefono ?? null;
@endphp

<section class="cm-section" @if(! ($conFondo ?? false)) style="padding-top: 56px" @endif>
    <div class="cm-container">
        <div class="cm-cta cm-reveal">
            <div>
                <h2>{{ $tituloCta ?? '¿Listo para vaciar tu obra?' }}</h2>
                <p>{{ $textoCta ?? 'Cuéntanos tu proyecto y te enviamos una cotización a la medida.' }}</p>
                <div class="cm-cta__buttons">
                    <a href="{{ request()->routeIs('home') ? '#cotizar' : route('home') . '#cotizar' }}" class="cm-btn cm-btn--dark"><i class="bi bi-calculator"></i> Solicitar cotización</a>
                    @if($telefonoLlamar)
                    <a href="tel:{{ preg_replace('/\D/', '', $telefonoLlamar) }}" class="cm-btn cm-btn--ghost"><i class="bi bi-telephone"></i> Llámanos</a>
                @endif
                </div>
            </div>
            <div class="cm-cta__art" aria-hidden="true">
                <img src="{{ asset('img/mixer.png') }}" alt="">
            </div>
        </div>
    </div>
</section>
