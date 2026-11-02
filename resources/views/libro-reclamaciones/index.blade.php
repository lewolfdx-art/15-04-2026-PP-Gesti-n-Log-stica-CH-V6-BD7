@extends('layouts.app')

@section('title', 'Libro de Reclamaciones - Concretera Mantaro')

@use('App\Models\LibroReclamacion')

@section('pagina_completa', true)

@section('content')
    @php
        $empresa = config('empresa');
    @endphp

    <style>
        [x-cloak] { display: none !important; }

        .lr-hero {
            background: linear-gradient(135deg, var(--color-dark) 0%, #0f1a24 100%);
            border-radius: 1rem;
            padding: 2.5rem 2rem;
            margin-bottom: 2rem;
            text-align: center;
            color: var(--color-white);
            border-bottom: 3px solid var(--color-primary);
        }
        .lr-hero h1 { font-size: 2.2rem; font-weight: 800; }
        .lr-hero h1 span { color: var(--color-primary); }
        .lr-hero p { max-width: 680px; margin: .5rem auto 0; color: #e2e2e2; }

        .lr-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid #efebe6;
            box-shadow: var(--cm-shadow);
            margin-bottom: 1.5rem;
        }
        .lr-proveedor { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: .75rem 1.5rem; font-size: .95rem; }
        .lr-proveedor strong { color: var(--color-dark); }

        .lr-section-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--color-dark);
            border-left: 4px solid var(--color-primary);
            padding-left: .75rem;
            margin-bottom: 1.25rem;
        }
        .lr-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem 1.5rem; }
        .lr-field label { display: block; font-weight: 600; font-size: .9rem; color: #374151; margin-bottom: .35rem; }
        .lr-field .required { color: #dc2626; }
        .lr-field input, .lr-field select, .lr-field textarea {
            width: 100%;
            border: 1.5px solid var(--cm-line);
            border-radius: 10px;
            padding: .75rem .9rem;
            font-size: .95rem;
            font-family: var(--cm-font);
            background: #fcfbf9;
        }
        .lr-field input:focus, .lr-field select:focus, .lr-field textarea:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(245, 129, 30, .15);
        }
        .lr-field small.error { color: #dc2626; display: block; margin-top: .25rem; }
        .lr-full { grid-column: 1 / -1; }

        .lr-opciones { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: .75rem; }
        .lr-field label.lr-opcion {
            display: flex; gap: .75rem; align-items: flex-start; margin-bottom: 0;
            border: 1px solid #e5e7eb; border-radius: .75rem; padding: .9rem 1rem; cursor: pointer;
        }
        .lr-opcion:has(input:checked) { border-color: var(--color-primary); background: #fff7ed; }
        .lr-field .lr-opcion input { width: auto; flex: none; margin-top: .25rem; accent-color: var(--color-primary); }
        .lr-opcion span { font-size: .85rem; color: #6b7280; display: block; }

        .lr-btn {
            background: var(--cm-orange); color: white; font-weight: 700; font-family: var(--cm-font);
            border: none; border-radius: 999px; padding: 1rem 2rem; cursor: pointer; font-size: 1rem;
            box-shadow: 0 10px 24px rgba(245, 129, 30, 0.35);
        }
        .lr-btn:hover { filter: brightness(.95); }

        .lr-ok { background: #ecfdf5; border: 1px solid #6ee7b7; color: #065f46; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; }
        .lr-ok .codigo { font-size: 1.6rem; font-weight: 800; letter-spacing: .03em; }
        .lr-error { background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; border-radius: .75rem; padding: 1rem 1.25rem; margin-bottom: 1.5rem; }
        .lr-nota { font-size: .85rem; color: #6b7280; }
    </style>

    @include('web.partials.page-hero', [
        'titulo' => 'Libro de',
        'resaltado' => 'Reclamaciones',
        'texto' => 'Conforme a lo establecido en el Código de Protección y Defensa del Consumidor (Ley N.° 29571), contamos con un Libro de Reclamaciones a tu disposición.',
        'miga' => 'Libro de Reclamaciones',
    ])

    <section class="cm-section cm-section--sand" style="padding-top: 56px">
    <div class="cm-container cm-narrow">

    @if (session('reclamo_codigo'))
        <div class="lr-ok">
            <p><i class="bi bi-check-circle-fill" style="margin-right: 6px"></i> Tu hoja de reclamación fue registrada con el número:</p>
            <p class="codigo">{{ session('reclamo_codigo') }}</p>
            <p>Te enviamos una copia a tu correo. Te responderemos a más tardar el <strong>{{ session('reclamo_fecha_limite') }}</strong>.</p>
        </div>
    @endif

    @error('general')
        <div class="lr-error"><i class="bi bi-exclamation-circle-fill" style="margin-right: 6px"></i> {{ $message }}</div>
    @enderror

    @if ($errors->any() && ! $errors->has('general'))
        <div class="lr-error"><i class="bi bi-exclamation-circle-fill" style="margin-right: 6px"></i> Revisa los campos marcados en rojo.</div>
    @endif

    <div class="lr-card">
        <div class="lr-proveedor">
            <div><strong>Proveedor:</strong> {{ $empresa['razon_social'] }}</div>
            @if ($empresa['ruc'])
                <div><strong>RUC:</strong> {{ $empresa['ruc'] }}</div>
            @endif
            @if ($empresa['direccion'])
                <div><strong>Dirección:</strong> {{ $empresa['direccion'] }}</div>
            @endif
            <div><strong>Fecha:</strong> {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    <form action="{{ route('reclamaciones.store') }}" method="POST" x-data="{ menor: {{ old('menor_edad') ? 'true' : 'false' }} }">
        @csrf

        {{-- 1. Consumidor --}}
        <div class="lr-card">
            <h2 class="lr-section-title">1. Identificación del consumidor reclamante</h2>
            <div class="lr-grid">
                <div class="lr-field lr-full">
                    <label for="claimant_name">Nombres y apellidos <span class="required">*</span></label>
                    <input id="claimant_name" name="claimant_name" value="{{ old('claimant_name') }}" required maxlength="255">
                    @error('claimant_name') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="lr-field">
                    <label for="document_type">Tipo de documento <span class="required">*</span></label>
                    <select id="document_type" name="document_type" required>
                        @foreach (LibroReclamacion::DOCUMENTOS as $valor => $texto)
                            <option value="{{ $valor }}" @selected(old('document_type', 'DNI') === $valor)>{{ $texto }}</option>
                        @endforeach
                    </select>
                    @error('document_type') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="lr-field">
                    <label for="document_number">N.° de documento <span class="required">*</span></label>
                    <input id="document_number" name="document_number" value="{{ old('document_number') }}" required maxlength="20">
                    @error('document_number') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="lr-field">
                    <label for="phone">Teléfono <span class="required">*</span></label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required maxlength="30">
                    @error('phone') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="lr-field">
                    <label for="email">Correo electrónico <span class="required">*</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255">
                    @error('email') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="lr-field lr-full">
                    <label for="address">Domicilio <span class="required">*</span></label>
                    <input id="address" name="address" value="{{ old('address') }}" required maxlength="500">
                    @error('address') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="lr-field lr-full">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 500;">
                        <input type="hidden" name="menor_edad" value="0">
                        <input type="checkbox" name="menor_edad" value="1" x-model="menor" style="width: auto;">
                        Soy menor de edad
                    </label>
                </div>
                <div class="lr-field lr-full" x-show="menor" x-cloak>
                    <label for="apoderado_nombre">Nombre del padre, madre o apoderado <span class="required">*</span></label>
                    <input id="apoderado_nombre" name="apoderado_nombre" value="{{ old('apoderado_nombre') }}" maxlength="255">
                    @error('apoderado_nombre') <small class="error">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        {{-- 2. Bien contratado --}}
        <div class="lr-card">
            <h2 class="lr-section-title">2. Identificación del bien contratado</h2>
            <div class="lr-grid">
                <div class="lr-field lr-full">
                    <div class="lr-opciones">
                        @foreach (LibroReclamacion::BIENES as $valor => $texto)
                            <label class="lr-opcion">
                                <input type="radio" name="bien_tipo" value="{{ $valor }}" @checked(old('bien_tipo', 'producto') === $valor) required>
                                <div><strong>{{ $texto }}</strong></div>
                            </label>
                        @endforeach
                    </div>
                    @error('bien_tipo') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="lr-field">
                    <label for="monto">Monto reclamado (S/)</label>
                    <input id="monto" name="monto" type="number" step="0.01" min="0" value="{{ old('monto') }}">
                    @error('monto') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="lr-field">
                    <label for="order_number">N.° de contrato, guía o pedido</label>
                    <input id="order_number" name="order_number" value="{{ old('order_number') }}" maxlength="100">
                    @error('order_number') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="lr-field lr-full">
                    <label for="bien_descripcion">Descripción del producto o servicio <span class="required">*</span></label>
                    <textarea id="bien_descripcion" name="bien_descripcion" rows="3" required maxlength="2000" placeholder="Ej: 15 m³ de concreto f'c 210 para losa aligerada, con bomba">{{ old('bien_descripcion') }}</textarea>
                    @error('bien_descripcion') <small class="error">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        {{-- 3. Detalle --}}
        <div class="lr-card">
            <h2 class="lr-section-title">3. Detalle de la reclamación y pedido del consumidor</h2>
            <div class="lr-grid">
                <div class="lr-field lr-full">
                    <div class="lr-opciones">
                        @foreach (LibroReclamacion::TIPOS as $valor => $texto)
                            <label class="lr-opcion">
                                <input type="radio" name="tipo" value="{{ $valor }}" @checked(old('tipo', 'reclamo') === $valor) required>
                                <div>
                                    <strong>{{ $texto }}</strong>
                                    <span>{{ LibroReclamacion::TIPOS_DESCRIPCION[$valor] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('tipo') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="lr-field lr-full">
                    <label for="claim_description">Detalle <span class="required">*</span></label>
                    <textarea id="claim_description" name="claim_description" rows="5" required maxlength="5000">{{ old('claim_description') }}</textarea>
                    @error('claim_description') <small class="error">{{ $message }}</small> @enderror
                </div>
                <div class="lr-field lr-full">
                    <label for="petition">Pedido <span class="required">*</span></label>
                    <textarea id="petition" name="petition" rows="3" required maxlength="5000" placeholder="¿Qué solución esperas?">{{ old('petition') }}</textarea>
                    @error('petition') <small class="error">{{ $message }}</small> @enderror
                </div>
            </div>
        </div>

        <div class="lr-card">
            <label style="display: flex; align-items: flex-start; gap: 8px;">
                <input type="checkbox" name="acepta" value="1" @checked(old('acepta')) required style="margin-top: .3rem; accent-color: var(--color-primary);">
                <span>Declaro que los datos consignados son verdaderos y acepto que la respuesta me sea enviada al correo electrónico indicado.</span>
            </label>
            @error('acepta') <small class="error" style="color:#dc2626;">{{ $message }}</small> @enderror

            <div style="margin-top: 24px">
                <button type="submit" class="lr-btn"><i class="bi bi-send" style="margin-right: 6px"></i> Enviar hoja de reclamación</button>
            </div>

            <div class="lr-nota" style="margin-top: 24px; display: grid; gap: 4px">
                <p>* La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI.</p>
                <p>* El proveedor deberá dar respuesta al reclamo o queja en un plazo no mayor a {{ $empresa['plazo_respuesta_dias'] }} días hábiles.</p>
            </div>
        </div>
    </form>
    </div>
    </section>
@endsection
