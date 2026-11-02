<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramacionResource\Pages\ListProgramacion;
use App\Filament\Resources\ProgramacionResource\Pages\ViewProgramacion;
use App\Models\Contrato;
use App\Models\Trabajador;
use App\Models\Vehiculo;
use App\Services\NotificationService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

/**
 * Programación de vaciados: fecha, horario, mixers/bombas y operadores de cada contrato.
 * No permite asignar un vehículo que ya está en otro vaciado en un horario que se cruza.
 * La usa el programador de vaciados (rol coordinador_de_vaciados); no muestra montos.
 */
class ProgramacionResource extends Resource
{
    protected static ?string $model = Contrato::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Operaciones';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Programación de vaciados';
    protected static ?string $modelLabel = 'Vaciado';
    protected static ?string $pluralModelLabel = 'Programación de vaciados';
    protected static ?int $navigationSort = 0;
    protected static ?string $slug = 'programacion';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'coordinador_de_vaciados']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $porProgramar = Contrato::whereNull('programado_at')->whereDate('fecha', '>=', today())->count();

        return $porProgramar > 0 ? (string) $porProgramar : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Vaciados por programar';
    }

    /**
     * Texto con los choques de vehículos para los datos que se están ingresando
     */
    private static function textoConflictos(Get $get, Contrato $record): HtmlString
    {
        $fecha = $get('fecha');
        $inicio = $get('hora_inicio');
        $fin = $get('hora_fin');
        $vehiculos = array_map('intval', (array) $get('vehiculos'));

        if (! $fecha || ! $inicio || ! $fin || $vehiculos === []) {
            return new HtmlString('<span style="color:#6b7280">Completa fecha, horario y vehículos para revisar disponibilidad.</span>');
        }

        $conflictos = Contrato::conflictosDeVehiculos($record->getKey(), Carbon::parse($fecha)->toDateString(), $inicio, $fin, $vehiculos);

        if ($conflictos->isEmpty()) {
            return new HtmlString('<span style="color:#16a34a;font-weight:600;display:inline-flex;align-items:center;gap:6px">'
                . svg('heroicon-o-check-circle', 'h-5 w-5')->toHtml() . 'Vehículos libres en ese horario.</span>');
        }

        $lineas = $conflictos->map(fn (Contrato $c) => '<li><b>' . e($c->vehiculos->pluck('codigo')->implode(', ')) . '</b> ocupado en '
            . e($c->nombre_cliente) . ' (' . e($c->horarioTexto()) . ')</li>')->implode('');

        return new HtmlString('<div style="color:#dc2626"><b style="display:inline-flex;align-items:center;gap:6px">'
            . svg('heroicon-o-exclamation-triangle', 'h-5 w-5')->toHtml() . 'Choque de vehículos:</b>'
            . "<ul style=\"margin:4px 0 0 18px;list-style:disc\">{$lineas}</ul></div>");
    }

    public static function accionProgramar(): Action
    {
        return Action::make('programar')
            ->label(fn (Contrato $record): string => $record->estaProgramado() ? 'Reprogramar' : 'Programar')
            ->icon('heroicon-o-calendar')
            ->color(fn (Contrato $record): string => $record->estaProgramado() ? 'gray' : 'primary')
            ->modalHeading(fn (Contrato $record): string => "Programar vaciado · {$record->nombre_cliente}")
            ->modalDescription(fn (Contrato $record): string => "{$record->volumen_guia} m³ · {$record->estructura} · {$record->bombeo}")
            ->fillForm(fn (Contrato $record): array => [
                'fecha'       => $record->fecha,
                'hora_inicio' => $record->hora_inicio,
                'hora_fin'    => $record->hora_fin,
                'vehiculos'   => $record->vehiculos()->pluck('vehiculos.id')->all(),
                'operadores'  => $record->operadores()->pluck('trabajadores.id')->all(),
            ])
            ->schema([
                DatePicker::make('fecha')->label('Fecha de vaciado')->native(false)->displayFormat('d/m/Y')->required()->live(),
                TimePicker::make('hora_inicio')->label('Hora de inicio')->seconds(false)->required()->live(onBlur: true),
                TimePicker::make('hora_fin')
                    ->label('Hora de término')
                    ->seconds(false)
                    ->required()
                    ->live(onBlur: true)
                    ->rule(fn (Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                        if ($get('hora_inicio') && $value <= $get('hora_inicio')) {
                            $fail('La hora de término debe ser después de la hora de inicio.');
                        }
                    }),
                Select::make('vehiculos')
                    ->label('Mixers y bombas')
                    ->options(fn () => Vehiculo::activos()->orderBy('codigo')->get()->mapWithKeys(fn (Vehiculo $v) => [$v->id => $v->etiqueta()]))
                    ->multiple()
                    ->required()
                    ->live()
                    ->columnSpanFull(),
                TextEntry::make('disponibilidad')
                    ->hiddenLabel()
                    ->state(fn (Get $get, Contrato $record): HtmlString => static::textoConflictos($get, $record))
                    ->columnSpanFull(),
                Select::make('operadores')
                    ->label('Operadores')
                    ->options(fn () => Trabajador::paraObras()->where('activo', true)->orderBy('nombre_completo')->get()
                        ->mapWithKeys(fn (Trabajador $t) => [$t->id => $t->nombre_completo . ' · '
                            . Trabajador::cargoTexto($t->tipo_cargo)
                            . ($t->user_id ? '' : ' (sin acceso)')]))
                    ->multiple()
                    ->searchable()
                    ->helperText('Los operadores con acceso reciben la obra con el mapa en su celular.')
                    ->columnSpanFull(),
            ])
            ->action(function (Contrato $record, array $data, Action $action): void {
                $fecha = Carbon::parse($data['fecha'])->toDateString();
                $vehiculos = array_map('intval', $data['vehiculos'] ?? []);

                // Se vuelve a revisar al guardar: otro programador pudo ocupar el vehículo mientras tanto
                $conflictos = Contrato::conflictosDeVehiculos($record->getKey(), $fecha, $data['hora_inicio'], $data['hora_fin'], $vehiculos);

                if ($conflictos->isNotEmpty()) {
                    Notification::make()
                        ->title('No se puede programar: hay vehículos ocupados')
                        ->body($conflictos->map(fn (Contrato $c) => $c->vehiculos->pluck('codigo')->implode(', ') . " en {$c->nombre_cliente} ({$c->horarioTexto()})")->implode("\n"))
                        ->danger()
                        ->persistent()
                        ->send();
                    $action->halt();
                }

                $cambioHorario = $record->estaProgramado()
                    && ($record->fecha?->toDateString() !== $fecha
                        || substr((string) $record->hora_inicio, 0, 5) !== substr($data['hora_inicio'], 0, 5));

                $record->forceFill([
                    'fecha'          => $fecha,
                    'hora_inicio'    => $data['hora_inicio'],
                    'hora_fin'       => $data['hora_fin'],
                    'programado_at'  => now(),
                    'programado_por' => auth()->id(),
                ])->save();

                $record->vehiculos()->sync($vehiculos);
                $record->operadores()->sync(array_map('intval', $data['operadores'] ?? []));

                // Si cambió la fecha u hora, los operadores vuelven a recibir el aviso con los datos nuevos
                if ($cambioHorario) {
                    $record->operadores()->newPivotQuery()->update(['notificado_at' => null]);
                }

                $avisados = NotificationService::notificarOperadores($record->fresh());

                Notification::make()
                    ->title("Vaciado programado: {$record->fecha->format('d/m/Y')} {$record->horarioTexto()}")
                    ->body($avisados > 0 ? "Se avisó a {$avisados} operador(es)." : null)
                    ->success()
                    ->send();
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')->label('Fecha')->date('d/m/Y')->sortable()->weight('bold')
                    ->description(fn (Contrato $record): ?string => $record->horarioTexto()),
                TextColumn::make('nombre_cliente')->label('Cliente')->searchable()->limit(28),
                TextColumn::make('ubicacion_referencia')->label('Lugar')->limit(30)->toggleable(),
                TextColumn::make('volumen_guia')->label('Volumen')->suffix(' m³'),
                TextColumn::make('estructura')->label('Vaciado')->toggleable(),
                TextColumn::make('bombeo')->label('Bombeo')->toggleable(),
                TextColumn::make('vehiculos.codigo')->label('Vehículos')->badge()->color('info')->placeholder('—'),
                TextColumn::make('operadores.nombre_completo')->label('Operadores')->badge()->color('gray')->placeholder('—')->toggleable(),
                TextColumn::make('programado_at')->label('Estado')->badge()
                    ->state(fn (Contrato $record): string => $record->estaProgramado() ? 'Programado' : 'Por programar')
                    ->color(fn (Contrato $record): string => $record->estaProgramado() ? 'success' : 'warning'),
            ])
            ->defaultSort('fecha', 'asc')
            ->recordActions([
                static::accionProgramar(),
                ViewAction::make()->label('Ver obra'),
            ])
            ->emptyStateHeading('No hay vaciados en esta vista');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Programación')
                    ->schema([
                        TextEntry::make('fecha')->label('Fecha')->date('d/m/Y'),
                        TextEntry::make('horario')->label('Horario')->state(fn (Contrato $record): ?string => $record->horarioTexto())->placeholder('Sin programar'),
                        TextEntry::make('vehiculos.codigo')->label('Vehículos')->badge()->placeholder('—'),
                        TextEntry::make('operadores.nombre_completo')->label('Operadores')->badge()->placeholder('—'),
                    ])
                    ->columns(['default' => 2, 'lg' => 4])
                    ->columnSpanFull(),

                Section::make('Ubicación')
                    ->schema([
                        ViewEntry::make('mapa')->hiddenLabel()->view('filament.infolists.mapa-obra'),
                        TextEntry::make('ubicacion_referencia')->label('Dirección y referencia')->placeholder('—'),
                    ])
                    ->columnSpanFull(),

                Section::make('Obra')
                    ->schema([
                        TextEntry::make('nombre_cliente')->label('Cliente'),
                        TextEntry::make('nombre_vendedor')->label('Asesor')->placeholder('—'),
                        TextEntry::make('estructura')->label('Tipo de vaciado')->placeholder('—'),
                        TextEntry::make('tipo_concreto')->label('Concreto')->placeholder('—'),
                        TextEntry::make('volumen_guia')->label('Volumen')->suffix(' m³'),
                        TextEntry::make('bombeo')->label('Bombeo')->placeholder('—'),
                        TextEntry::make('viabilidad')->label('Viabilidad')->badge()
                            ->formatStateUsing(fn (?string $state): string => Contrato::VIABILIDADES[$state] ?? '—')
                            ->color(fn (?string $state): string => Contrato::VIABILIDAD_COLORES[$state] ?? 'gray')
                            ->icon(fn (?string $state): ?string => Contrato::VIABILIDAD_ICONOS[$state] ?? null),
                        TextEntry::make('viabilidad_comentario')->label('Comentarios')->placeholder('—'),
                        TextEntry::make('descripcion_lugar')->label('Descripción del lugar')->placeholder('—')->columnSpanFull(),
                        ImageEntry::make('fotos_lugar')->label('Fotos')->disk('public')->imageHeight(140)->placeholder('Sin fotos')->columnSpanFull(),
                    ])
                    ->columns(['default' => 2, 'lg' => 4])
                    ->columnSpanFull(),
            ]);
    }

    public static function getWidgets(): array
    {
        return [\App\Filament\Widgets\FechasSeparadasWidget::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProgramacion::route('/'),
            'view'  => ViewProgramacion::route('/{record}'),
        ];
    }
}
