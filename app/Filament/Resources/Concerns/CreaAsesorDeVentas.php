<?php

namespace App\Filament\Resources\Concerns;

use Filament\Schemas\Components\Utilities\Get;
use App\Models\Trabajador;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

trait CreaAsesorDeVentas
{
    /**
     * Opciones del Select de asesores de ventas activos, por nombre.
     */
    protected static function opcionesAsesoresVentas(): array
    {
        return Trabajador::asesores()
            ->where('activo', true)
            ->orderBy('nombre_completo')
            ->pluck('nombre_completo', 'nombre_completo')
            ->toArray();
    }

    /**
     * Formulario para crear un nuevo asesor de ventas desde el Select.
     */
    protected static function formularioCrearAsesor(): array
    {
        return [
            Select::make('tipo_cargo')
                ->label('Cargo / Tipo')
                ->options([
                    'asesor_ventas' => '💼 Asesor de Ventas',
                ])
                ->default('asesor_ventas')
                ->required()
                ->disabled(),

            TextInput::make('nombre_completo')
                ->label('Nombre Completo del Asesor')
                ->required()
                ->maxLength(255),

            TextInput::make('dni')
                ->label('DNI')
                ->required()
                ->mask('99999999')
                ->rule('digits:8')
                ->unique('trabajadores', 'dni', ignoreRecord: true),

            Toggle::make('tiene_fecha_nacimiento')
                ->label('¿Tiene fecha de nacimiento?')
                ->default(true)
                ->live()
                ->columnSpanFull(),

            DatePicker::make('fecha_nacimiento')
                ->label('Fecha de Nacimiento')
                ->default(now()->subYears(25))
                ->native(false)
                ->displayFormat('d/m/Y')
                ->format('Y-m-d')
                ->maxDate(now())
                ->closeOnDateSelection(true)
                ->placeholder('dd/mm/yyyy')
                ->required(fn (Get $get) => $get('tiene_fecha_nacimiento') === true)
                ->disabled(fn (Get $get) => $get('tiene_fecha_nacimiento') === false)
                ->dehydrated(fn (Get $get) => $get('tiene_fecha_nacimiento') === true)
                ->rules(fn (Get $get) => $get('tiene_fecha_nacimiento') === true ? ['required', 'date'] : ['nullable', 'date'])
                ->afterStateHydrated(function ($state, callable $set) {
                    if (empty($state)) {
                        $set('tiene_fecha_nacimiento', false);
                    }
                }),

            Textarea::make('descripcion')
                ->label('Descripción / Nota')
                ->rows(2),
        ];
    }

    /**
     * Crea el trabajador asesor y devuelve su nombre completo para el Select.
     */
    protected static function crearAsesorVentas(array $data): string
    {
        $nuevoTrabajador = Trabajador::create([
            'tipo_cargo' => 'asesor_ventas',
            'nombre_completo' => strtoupper(trim($data['nombre_completo'])),
            'dni' => $data['dni'] ?? null,
            'fecha_nacimiento' => $data['tiene_fecha_nacimiento'] ? ($data['fecha_nacimiento'] ?? null) : null,
            'descripcion' => $data['descripcion'] ?? null,
            'activo' => true,
        ]);

        return $nuevoTrabajador->nombre_completo;
    }
}
