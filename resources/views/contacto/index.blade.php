@extends('layouts.app')

@section('title', 'Contacto - Concretera Mantaro')

@section('content')
    @php
        $config = App\Models\Inicio::getActiveContent();
    @endphp

    <style>
        .contact-hero {
            background: linear-gradient(135deg, var(--color-dark) 0%, #0f1a24 100%);
            border-radius: 1rem;
            padding: 3rem 2rem;
            margin-bottom: 3rem;
            text-align: center;
            color: var(--color-white);
            border-bottom: 3px solid var(--color-primary);
        }
        .contact-hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
        }
        .contact-hero h1 span {
            color: var(--color-primary);
        }
        .contact-hero p {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
            color: #e2e2e2;
        }

        .map-container {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 3rem;
        }
        .map-container iframe {
            width: 100%;
            height: 400px;
            border: none;
        }

        .contact-info-side {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            height: 100%;
        }
        .contact-info-side .info-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .contact-info-side .info-item:last-child {
            border-bottom: none;
        }
        .contact-info-side .info-item i {
            font-size: 1.5rem;
            color: var(--color-primary);
            width: 2rem;
            margin-top: 0.2rem;
        }
        .contact-info-side .info-item h4 {
            font-weight: 600;
            color: var(--color-dark);
            margin-bottom: 0.2rem;
        }
        .contact-info-side .info-item p {
            color: #4b5563;
            margin: 0;
        }
        .contact-info-side .info-item a {
            color: var(--color-primary);
            text-decoration: none;
        }
        .contact-info-side .info-item a:hover {
            text-decoration: underline;
        }

        .form-section {
            background: #f8f9fa;
            border-radius: 1rem;
            padding: 2.5rem;
            height: 100%;
        }
        .form-section h2 {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--color-dark);
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-section h2 span {
            color: var(--color-primary);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--color-dark);
            margin-bottom: 0.5rem;
        }
        .form-group label .required {
            color: #ef4444;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border 0.3s ease;
            background: white;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(245, 129, 30, 0.2);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .btn-enviar {
            background: var(--color-primary);
            color: white;
            padding: 0.75rem 2.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-enviar:hover {
            background: var(--color-primary-dark);
            transform: scale(1.02);
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #10b981;
        }

        .contact-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        @media (max-width: 768px) {
            .contact-hero h1 { font-size: 1.8rem; }
            .map-container iframe { height: 250px; }
            .contact-row { grid-template-columns: 1fr; }
            .form-section { padding: 1.5rem; }
            .contact-info-side { padding: 1.5rem; }
        }
    </style>

    <!-- HERO -->
    <div class="contact-hero">
        <h1>Contáctanos</h1>
        <p>Estamos aquí para ayudarte con tu proyecto de construcción. Comunícate con nosotros y te atenderemos de inmediato.</p>
    </div>

    <!-- MAPA CON UBICACIÓN EXACTA DE CONCRETERA MANTARO -->
    <div class="map-container">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15605.57211908587!2d-75.210456!3d-12.06789!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x910e8b4b8b8b8b8b%3A0x8b8b8b8b8b8b8b8b!2sHuancayo%2C%20Peru!5e0!3m2!1ses!2spe!4v1700000000000" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade"
            style="width:100%; height:400px; border:0;">
        </iframe>
    </div>

    <!-- FILA: FORMULARIO + INFORMACIÓN DE CONTACTO -->
    <div class="contact-row">
        <!-- FORMULARIO -->
        <div class="form-section">
            <h2>Envíanos un <span>mensaje</span></h2>

            @if(session('success'))
                <div class="alert-success">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            @error('general')
                <div class="alert-success" style="background:#fef2f2;border-color:#fca5a5;color:#991b1b;">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ $message }}
                </div>
            @enderror

            <form action="{{ route('contacto.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Nombre completo <span class="required">*</span></label>
                    <input type="text" id="name" name="name" placeholder="Ej: Andrés Rodríguez" required value="{{ old('name') }}">
                    @error('name') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="email">Correo electrónico <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Ej: andres@correo.com" required value="{{ old('email') }}">
                    @error('email') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Teléfono</label>
                    <input type="tel" id="phone" name="phone" placeholder="Ej: 925 091 695" value="{{ old('phone') }}">
                    @error('phone') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="subject">Asunto <span class="required">*</span></label>
                    <input type="text" id="subject" name="subject" placeholder="Ej: Cotización de concreto" required value="{{ old('subject') }}">
                    @error('subject') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="message">Mensaje <span class="required">*</span></label>
                    <textarea id="message" name="message" rows="5" placeholder="Escribe tu mensaje aquí..." required>{{ old('message') }}</textarea>
                    @error('message') <small class="text-red-500">{{ $message }}</small> @enderror
                </div>

                <button type="submit" class="btn-enviar">
                    <i class="fas fa-paper-plane mr-2"></i> Enviar mensaje
                </button>
            </form>
        </div>

        <!-- INFORMACIÓN DE CONTACTO (lado derecho) -->
        <div class="contact-info-side">
            <h3 class="text-xl font-bold text-[#16222e] mb-4" style="color: var(--color-dark);">Información de contacto</h3>

            <div class="info-item">
                <i class="fas fa-phone-alt"></i>
                <div>
                    <h4>Teléfono</h4>
                    <p><a href="tel:982337770">982 337 770</a></p>
                    <p><a href="tel:957924586">957 924 586</a></p>
                    <p><a href="https://wa.me/51957924586" target="_blank">WhatsApp: 957 924 586</a></p>
                </div>
            </div>

            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <div>
                    <h4>Email</h4>
                    <p><a href="mailto:amgeotop@gmail.com">amgeotop@gmail.com</a></p>
                </div>
            </div>

            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <h4>Dirección</h4>
                    <p>Pasaje Miraflores, Huancayo</p>
                    <p><a href="https://maps.app.goo.gl/iCCVn77fkMQdyVuv6" target="_blank" class="text-[#f5811e]">Ver en Google Maps</a></p>
                </div>
            </div>
        </div>
    </div>

@endsection