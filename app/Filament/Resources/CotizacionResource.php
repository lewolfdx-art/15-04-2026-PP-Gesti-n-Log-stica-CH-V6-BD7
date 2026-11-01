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

    public static function accionAdelanto(): Action
    {
        return Action::make('registrar_adelanto')
            ->label('Registrar adelanto')
            ->icon('heroicon-o-banknotes')
            ->color('info')
            ->visible(fn (Cotizacion $record): bool => $record->puedeModificarse())
            ->modalDescription('La cotización queda "En espera" hasta pasarla a contrato. El adelanto se registrará como ingreso al crear el contrato.')
            ->schema([
                TextInput::make('monto')->label('Monto del adelanto')->prefix('S/')->numeric()->minValue(0.01)->required()
                    ->default(fn (Cotizacion $record) => $record->adelanto_monto),
                Select::make('forma_pago')->label('Forma de pago')->options(PagoPersonal::FORMAS_PAGO)->default('EFECTIVO')->required(),
            ])
            ->action(function (Cotizacion $record, array $data): void {
                $record->registrarAdelanto((float) $data['monto'], $data['forma_pago'], auth()->user());

                NotificationService::notificarAsesor($record, "💵 Adelanto registrado · {$record->codigo}",
                    "El cliente {$record->cliente_nombre} dio un adelanto de S/ " . number_format((float) $record->adelanto_monto, 2) . '.');

                Notification::make()->title("Adelanto registrado. {$record->codigo} queda en espera.")->success()->send();
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
                TextInput::make('numero_contrato')->label('N.° de contrato')->maxLength(50),
            ])
            ->action(function (Cotizacion $record, array $data, $livewire): void {
                try {
                    $contrato = $record->convertirEnContrato($data, auth()->user());
                } catch (InvalidArgumentException $e) {
                    Notification::make()->title($e->getMessage())->danger()->send();

                    return;
                }

                NotificationService::notificarVaciadoPorProgramar($contrato);
                NotificationService::notificarAsesor($record, "✅ {$record->codigo} pasó a contrato",
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

                NotificationService::notificarAsesor($record, "❌ {$record->codigo} rechazada", $data['motivo']);

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
                TextColumn::make('adelanto_monto')->label('Adelanto')->money('PEN')->placeholder('—'),
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
                        TextEntry::make('adelanto_monto')->label('Adelanto')->money('PEN')->placeholder('—')
                            ->helperText(fn (Cotizacion $record): ?string => $record->adelanto_at?->format('d/m/Y H:i')),
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
                            ->color(fn (?string $state): string => Contrato::VIABILIDAD_COLORES[$state] ?? 'gray'),
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
