<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CotizacionResource\Pages\ListCotizaciones;
use App\Filament\Resources\CotizacionResource\Pages\ViewCotizacion;
use App\Models\Contrato;
use App\Models\Cotizacion;
use App\Models\PagoPersonal;
use App\Services\NotificationService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Bandeja de cotizaciones que envían los asesores desde la obra.
 * El admin registra el adelanto (queda en espera), la pasa a contrato o la rechaza.
 */
class CotizacionResource extends Resource
{
    use \App\Filament\Concerns\SoloAdministradores;

    protected static ?string $model = Cotizacion::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Comercial';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Cotizaciones';
    protected static ?string $modelLabel = 'Cotización';
    protected static ?string $pluralModelLabel = 'Cotizaciones';
    protected static ?int $navigationSort = 0;
    protected static ?string $slug = 'cotizaciones';

    protected static ?string $recordTitleAttribute = 'codigo';

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
        $pendientes = Cotizacion::porRevisar()->count();

        return $pendientes > 0 ? (string) $pendientes : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Cotizaciones por revisar o en espera';
    }

    /**
     * Confirmar el adelanto (el que anotó el asesor o uno que llegó a la oficina): la fecha queda separada.
     * Avisa si ese día y hora ya hay otra fecha separada o un vaciado programado.
     */
    public static function accionAdelanto(): Action
    {
        return Action::make('registrar_adelanto')
            ->label(fn (Cotizacion $record): string => $record->tieneAdelantoPorConfirmar() ? 'Confirmar adelanto' : 'Registrar adelanto')
            ->icon('heroicon-o-banknotes')
            ->color(fn (Cotizacion $record): string => $record->tieneAdelantoPorConfirmar() ? 'warning' : 'info')
            ->visible(fn (Cotizacion $record): bool => $record->puedeModificarse())
            ->modalHeading(fn (Cotizacion $record): string => "Adelanto de {$record->cliente_nombre}")
            ->modalDescription('Al confirmarlo, la fecha y hora quedan separadas para este cliente y se avisa al programador de vaciados. El adelanto entra a Ingresos cuando la cotización pasa a contrato.')
            ->schema([
                TextEntry::make('declarado')
                    ->label('Lo que anotó el asesor')
                    ->state(fn (Cotizacion $record): string => 'S/ ' . number_format((float) $record->adelanto_declarado_monto, 2)
                        . ' · ' . (PagoPersonal::FORMAS_PAGO[$record->adelanto_declarado_forma_pago] ?? $record->adelanto_declarado_forma_pago ?? '—')
                        . ' · ' . $record->adelanto_declarado_at?->format('d/m/Y H:i'))
                    ->visible(fn (Cotizacion $record): bool => (float) $record->adelanto_declarado_monto > 0),
                ImageEntry::make('adelanto_voucher')
                    ->label('Voucher')
                    ->disk('public')
                    ->imageHeight(220)
                    ->visible(fn (Cotizacion $record): bool => filled($record->adelanto_voucher)),
                TextInput::make('monto')->label('Monto recibido')->prefix('S/')->numeric()->minValue(0.01)->required()
                    ->default(fn (Cotizacion $record) => $record->adelanto_monto ?? $record->adelanto_declarado_monto),
                Select::make('forma_pago')->label('Forma de pago')->options(PagoPersonal::FORMAS_PAGO)->required()
                    ->default(fn (Cotizacion $record) => $record->adelanto_forma_pago ?? $record->adelanto_declarado_forma_pago ?? 'EFECTIVO'),
                DatePicker::make('fecha_tentativa')->label('Fecha que separa')->native(false)->displayFormat('d/m/Y')->required()
                    ->default(fn (Cotizacion $record) => $record->fecha_tentativa),
                \Filament\Forms\Components\TimePicker::make('hora_tentativa')->label('Hora')->seconds(false)->required()
                    ->default(fn (Cotizacion $record) => $record->horaTexto()),
                TextEntry::make('cruces')
                    ->label('Ojo con el horario')
                    ->state(fn (Cotizacion $record): string => implode("\n", $record->cruces()))
                    ->color('danger')
                    ->visible(fn (Cotizacion $record): bool => $record->cruces() !== []),
            ])
            ->action(function (Cotizacion $record, array $data): void {
                $record->forceFill([
                    'fecha_tentativa' => $data['fecha_tentativa'],
                    'hora_tentativa'  => $data['hora_tentativa'],
                ])->save();

                $cruces = $record->cruces();
                $record->registrarAdelanto((float) $data['monto'], $data['forma_pago'], auth()->user());

                NotificationService::notificarAsesor($record, "Adelanto confirmado · {$record->codigo}",
                    "La fecha de {$record->cliente_nombre} quedó separada para el {$record->fecha_tentativa->format('d/m/Y')} a las {$record->horaTexto()}.");
                NotificationService::notificarFechaSeparada($record);

                Notification::make()
                    ->title("Adelanto confirmado. {$record->codigo} tiene la fecha separada.")
                    ->body($cruces ? "Ojo, ese horario se cruza con:\n" . implode("\n", $cruces) : null)
                    ->status($cruces ? 'warning' : 'success')
                    ->persistent($cruces !== [])
                    ->send();
            });
    }

    /**
     * El cliente desiste después de separar: se libera la fecha y se registra la devolución del adelanto.
     */
    public static function accionCancelar(): Action
    {
        return Action::make('cancelar')
            ->label('Cancelar y devolver adelanto')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('danger')
            ->visible(fn (Cotizacion $record): bool => $record->estado === 'en_espera')
            ->modalHeading(fn (Cotizacion $record): string => "Cancelar la separación de {$record->cliente_nombre}")
            ->modalDescription(fn (Cotizacion $record): string => 'Se libera la fecha y se registra que se le devolvió el adelanto de S/ '
                . number_format((float) $record->adelanto_monto, 2) . '. Esta acción no se puede deshacer.')
            ->schema([
                Textarea::make('motivo')->label('Motivo de la cancelación')->required()->rows(2),
                Select::make('forma_devolucion')->label('¿Cómo se le devolvió?')->options(PagoPersonal::FORMAS_PAGO)->required()
                    ->default(fn (Cotizacion $record) => $record->adelanto_forma_pago),
            ])
            ->action(function (Cotizacion $record, array $data): void {
                try {
                    $record->cancelarConDevolucion($data['motivo'], $data['forma_devolucion'], auth()->user());
                } catch (InvalidArgumentException $e) {
                    Notification::make()->title($e->getMessage())->danger()->send();

                    return;
                }

                NotificationService::notificarAsesor($record, "{$record->codigo} cancelada",
                    "{$record->cliente_nombre} canceló. Se le devolvió el adelanto de S/ " . number_format((float) $record->adelanto_monto, 2) . '.');
                NotificationService::notificarFechaSeparada($record, liberada: true);

                Notification::make()->title("{$record->codigo} cancelada. La fecha quedó libre.")->success()->send();
            });
    }

    public static function accionPasarAContrato(): Action
    {
        return Action::make('pasar_a_contrato')
            ->label('Pasar a contrato')
            ->icon('heroicon-o-document-check')
            ->color('success')
            ->visible(fn (Cotizacion $record): bool => $record->puedeModificarse())
            ->modalHeading(fn (Cotizacion $record): string => "Pasar {$record->codigo} a contrato")
            ->modalDescription('Se crea el contrato con todo lo que registró el asesor (ubicación, fotos, viabilidad, medición) y se avisa al programador de vaciados.')
            ->schema([
                TextInput::make('precio_unitario_aprobado')
                    ->label('Precio aprobado por m³')
                    ->prefix('S/')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->default(fn (Cotizacion $record) => $record->precio_unitario_propuesto)
                    ->helperText(fn (Cotizacion $record): string => 'Propuesto por el asesor: S/ ' . number_format((float) $record->precio_unitario_propuesto, 2)),
                DatePicker::make('fecha')
                    ->label('Fecha de vaciado')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->required()
                    ->default(fn (Cotizacion $record) => $record->fecha_tentativa ?? today()),
                // Obligatorio: liga el adelanto y los pagos al contrato. Se guarda como "C-123", igual que en Contratos
                TextInput::make('numero_contrato')
                    ->label('N.° de contrato')
                    ->prefix('C-')
                    ->required()
                    ->regex('/^\d+$/')
                    ->validationMessages(['regex' => 'Escribe solo los números del contrato, sin "C-".'])
                    ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                        if (Contrato::where('numero_contrato', 'C-' . $value)->exists()) {
                            $fail("Ya existe el contrato C-{$value}.");
                        }
                    })
                    ->maxLength(10),
            ])
            ->action(function (Cotizacion $record, array $data, $livewire): void {
                try {
                    $contrato = $record->convertirEnContrato($data, auth()->user());
                } catch (InvalidArgumentException $e) {
                    Notification::make()->title($e->getMessage())->danger()->send();

                    return;
                }

                NotificationService::notificarVaciadoPorProgramar($contrato);
                NotificationService::notificarAsesor($record, "{$record->codigo} pasó a contrato",
                    "La obra de {$record->cliente_nombre} ya es un contrato y pasa a programación.");

                Notification::make()
                    ->title("Contrato creado desde {$record->codigo}")
                    ->body('Revisa los datos del contrato. El programador de vaciados ya fue avisado.')
                    ->success()
                    ->send();

                $livewire->redirect(ContratoResource::getUrl('edit', ['record' => $contrato]));
            });
    }

    public static function accionRechazar(): Action
    {
        return Action::make('rechazar')
            ->label('Rechazar')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn (Cotizacion $record): bool => $record->puedeModificarse())
            ->schema([
                Textarea::make('motivo')->label('Motivo')->required()->rows(3),
            ])
            ->action(function (Cotizacion $record, array $data): void {
                $record->rechazar($data['motivo'], auth()->user());

                NotificationService::notificarAsesor($record, "{$record->codigo} rechazada", $data['motivo']);

                Notification::make()->title("{$record->codigo} rechazada")->success()->send();
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')->label('N.°')->weight('bold')->searchable(),
                TextColumn::make('enviada_at')->label('Enviada')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('asesor.nombre_completo')->label('Asesor')->searchable(),
                TextColumn::make('cliente_nombre')->label('Cliente')->searchable()->limit(28),
                TextColumn::make('ubicacion_referencia')->label('Lugar')->limit(30)->toggleable(),
                TextColumn::make('volumen_estimado')->label('Volumen')->suffix(' m³'),
                TextColumn::make('monto')->label('Monto estimado')
                    ->state(fn (Cotizacion $record): ?float => $record->montoEstimado())
                    ->money('PEN'),
                TextColumn::make('adelanto')
                    ->label('Adelanto')
                    ->state(fn (Cotizacion $record): ?string => match (true) {
                        (float) $record->adelanto_monto > 0 => 'S/ ' . number_format((float) $record->adelanto_monto, 2),
                        $record->tieneAdelantoPorConfirmar() => 'S/ ' . number_format((float) $record->adelanto_declarado_monto, 2),
                        default => null,
                    })
                    ->description(fn (Cotizacion $record): ?string => match (true) {
                        $record->estado === 'cancelada' => 'Devuelto',
                        (float) $record->adelanto_monto > 0 => 'Confirmado',
                        $record->tieneAdelantoPorConfirmar() => 'Por confirmar',
                        default => null,
                    })
                    ->color(fn (Cotizacion $record): string => $record->tieneAdelantoPorConfirmar() ? 'warning' : 'gray')
                    ->placeholder('—'),
                TextColumn::make('fecha_tentativa')
                    ->label('Fecha pedida')
                    ->date('d/m/Y')
                    ->description(fn (Cotizacion $record): ?string => $record->horaTexto())
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn (string $state): string => Cotizacion::ESTADOS[$state] ?? $state)
                    ->color(fn (string $state): string => Cotizacion::ESTADO_COLORES[$state] ?? 'gray'),
            ])
            ->defaultSort('enviada_at', 'desc')
            ->filters([
                SelectFilter::make('estado')->label('Estado')->options(Cotizacion::ESTADOS),
                SelectFilter::make('asesor_id')->label('Asesor')->relationship('asesor', 'nombre_completo'),
            ])
            ->recordActions([
                ViewAction::make()->label('Revisar'),
                static::accionAdelanto(),
                static::accionPasarAContrato(),
                static::accionCancelar(),
            ])
            ->emptyStateHeading('Sin cotizaciones')
            ->emptyStateDescription('Las cotizaciones que los asesores registren desde la obra aparecerán aquí.');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cotización')
                    ->schema([
                        TextEntry::make('codigo')->label('N.°')->weight('bold'),
                        TextEntry::make('estado')->label('Estado')->badge()
                            ->formatStateUsing(fn (string $state): string => Cotizacion::ESTADOS[$state] ?? $state)
                            ->color(fn (string $state): string => Cotizacion::ESTADO_COLORES[$state] ?? 'gray'),
                        TextEntry::make('enviada_at')->label('Enviada')->dateTime('d/m/Y H:i'),
                        TextEntry::make('asesor.nombre_completo')->label('Asesor'),
                        TextEntry::make('adelanto_monto')->label('Adelanto confirmado')->money('PEN')->placeholder('—')
                            ->helperText(fn (Cotizacion $record): ?string => $record->adelanto_at?->format('d/m/Y H:i')),
                        TextEntry::make('adelanto_declarado_monto')->label('Adelanto que anotó el asesor')->money('PEN')
                            ->color(fn (Cotizacion $record): string => $record->tieneAdelantoPorConfirmar() ? 'warning' : 'gray')
                            ->helperText(fn (Cotizacion $record): ?string => collect([
                                PagoPersonal::FORMAS_PAGO[$record->adelanto_declarado_forma_pago] ?? null,
                                $record->tieneAdelantoPorConfirmar() ? 'por confirmar' : null,
                            ])->filter()->implode(' · ') ?: null)
                            ->visible(fn (Cotizacion $record): bool => (float) $record->adelanto_declarado_monto > 0),
                        ImageEntry::make('adelanto_voucher')->label('Voucher del adelanto')->disk('public')->imageHeight(160)
                            ->visible(fn (Cotizacion $record): bool => filled($record->adelanto_voucher)),
                        TextEntry::make('motivo_cancelacion')->label('Cancelada')->color('danger')
                            ->helperText(fn (Cotizacion $record): ?string => $record->devolucion_at
                                ? 'Adelanto devuelto el ' . $record->devolucion_at->format('d/m/Y') . ' · ' . (PagoPersonal::FORMAS_PAGO[$record->devolucion_forma_pago] ?? $record->devolucion_forma_pago)
                                : null)
                            ->visible(fn (Cotizacion $record): bool => $record->estado === 'cancelada')->columnSpanFull(),
                        TextEntry::make('contrato.numero_contrato')->label('Contrato')->placeholder('—')
                            ->url(fn (Cotizacion $record): ?string => $record->contrato ? ContratoResource::getUrl('edit', ['record' => $record->contrato]) : null),
                        TextEntry::make('motivo_rechazo')->label('Motivo del rechazo')->color('danger')
                            ->visible(fn (Cotizacion $record): bool => $record->estado === 'rechazada')->columnSpanFull(),
                    ])
                    ->columns(['default' => 2, 'lg' => 3])
                    ->columnSpanFull(),

                Section::make('Ubicación de la obra')
                    ->schema([
                        ViewEntry::make('mapa')->hiddenLabel()->view('filament.infolists.mapa-obra'),
                        TextEntry::make('ubicacion_referencia')->label('Dirección y referencia'),
                    ])
                    ->columnSpanFull(),

                Section::make('Cliente y medición')
                    ->schema([
                        TextEntry::make('cliente_nombre')->label('Cliente'),
                        TextEntry::make('cliente_telefono')->label('Teléfono')->placeholder('—'),
                        TextEntry::make('cliente_documento')->label('DNI / RUC')->placeholder('—'),
                        TextEntry::make('estructura')->label('Tipo de vaciado'),
                        TextEntry::make('tipo_concreto')->label('Concreto')->placeholder('—'),
                        TextEntry::make('volumen_estimado')->label('Volumen estimado')->suffix(' m³'),
                        TextEntry::make('bombeo')->label('Bombeo')->placeholder('—'),
                        TextEntry::make('fecha_tentativa')->label('Fecha tentativa')->date('d/m/Y')->placeholder('—')
                            ->helperText(fn (Cotizacion $record): ?string => $record->hora_tentativa ? 'Hora: ' . substr($record->hora_tentativa, 0, 5) : null),
                        TextEntry::make('precio_unitario_propuesto')->label('Precio propuesto por m³')->money('PEN'),
                        TextEntry::make('monto')->label('Monto estimado')->state(fn (Cotizacion $record) => $record->montoEstimado())->money('PEN')->weight('bold'),
                    ])
                    ->columns(['default' => 2, 'lg' => 3])
                    ->columnSpanFull(),

                Section::make('El lugar')
                    ->schema([
                        TextEntry::make('viabilidad')->label('Viabilidad')->badge()
                            ->formatStateUsing(fn (?string $state): string => Contrato::VIABILIDADES[$state] ?? '—')
                            ->color(fn (?string $state): string => Contrato::VIABILIDAD_COLORES[$state] ?? 'gray')
                            ->icon(fn (?string $state): ?string => Contrato::VIABILIDAD_ICONOS[$state] ?? null),
                        TextEntry::make('viabilidad_comentario')->label('Comentarios')->placeholder('—'),
                        TextEntry::make('descripcion_lugar')->label('Descripción del lugar')->placeholder('—')->columnSpanFull(),
                        ImageEntry::make('fotos_lugar')->label('Fotos')->disk('public')->imageHeight(150)->placeholder('Sin fotos')->columnSpanFull(),
                        TextEntry::make('observaciones')->label('Observaciones del asesor')->placeholder('—')->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCotizaciones::route('/'),
            'view'  => ViewCotizacion::route('/{record}'),
        ];
    }
}
