<?php

namespace App\Filament\Operador\Resources;

use App\Filament\Operador\Resources\MisCotizacionesResource\Pages\CreateMiCotizacion;
use App\Filament\Operador\Resources\MisCotizacionesResource\Pages\EditMiCotizacion;
use App\Filament\Operador\Resources\MisCotizacionesResource\Pages\ListMisCotizaciones;
use App\Filament\Operador\Resources\MisCotizacionesResource\Pages\ViewMiCotizacion;
use App\Models\Contrato;
use App\Models\Cotizacion;
use App\Models\PagoPersonal;
use Filament\Forms\Components\Toggle;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ViewField;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Cotizaciones que el asesor de ventas registra en la obra, desde su celular.
 * Solo ve las suyas y solo puede corregirlas mientras estén "Por revisar".
 */
class MisCotizacionesResource extends Resource
{
    protected static ?string $model = Cotizacion::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Mis cotizaciones';
    protected static ?string $modelLabel = 'Cotización';
    protected static ?string $pluralModelLabel = 'Mis cotizaciones';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'cotizaciones';

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('asesor') ?? false;
    }

    public static function asesorId(): ?int
    {
        return auth()->user()?->trabajador?->getKey();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('asesor_id', static::asesorId() ?? 0);
    }

    /**
     * Si el asesor apaga "El cliente dejó un adelanto", se borra lo que había anotado
     * (los campos ocultos no se envían y quedaría el valor anterior).
     */
    public static function limpiarAdelantoSiNoHay(array $data, array $estadoFormulario): array
    {
        if (empty($estadoFormulario['dejo_adelanto'])) {
            $data['adelanto_declarado_monto'] = null;
            $data['adelanto_declarado_forma_pago'] = null;
            $data['adelanto_voucher'] = null;
        }

        return $data;
    }

    public static function canEdit(Model $record): bool
    {
        return $record->estado === 'enviada';
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cliente')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('cliente_nombre')->label('Nombre o razón social')->required()->maxLength(255)->columnSpanFull(),
                        TextInput::make('cliente_telefono')->label('Teléfono')->tel()->maxLength(30),
                        TextInput::make('cliente_documento')->label('DNI / RUC')->maxLength(20),
                    ])
                    ->columns(2),

                Section::make('Ubicación de la obra')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        ViewField::make('mapa')->hiddenLabel()->view('filament.forms.mapa-ubicacion')->dehydrated(false)->columnSpanFull(),
                        TextInput::make('latitud')->label('Latitud')->numeric()->minValue(-90)->maxValue(90),
                        TextInput::make('longitud')->label('Longitud')->numeric()->minValue(-180)->maxValue(180),
                        Textarea::make('ubicacion_referencia')->label('Dirección y referencia')->placeholder('Ej: Jr. Ancash 123, frente al colegio')->required()->rows(2)->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Medición y vaciado')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Select::make('estructura')->label('Tipo de vaciado')->options(Contrato::ESTRUCTURAS)->required()->native(false),
                        Select::make('tipo_concreto')->label('Concreto (f\'c)')->options(Contrato::TIPOS_CONCRETO)->native(false),
                        TextInput::make('volumen_estimado')->label('Volumen estimado')->suffix('m³')->numeric()->minValue(0.5)->required()->live(onBlur: true),
                        Select::make('bombeo')->label('Bombeo')->options(Contrato::BOMBEOS)->native(false),
                        DatePicker::make('fecha_tentativa')->label('Fecha tentativa de vaciado')->native(false)->displayFormat('d/m/Y')->minDate(today()),
                        TimePicker::make('hora_tentativa')->label('Hora tentativa')->seconds(false),
                        TextInput::make('precio_unitario_propuesto')->label('Precio propuesto por m³')->prefix('S/')->numeric()->minValue(0)->required()->live(onBlur: true),
                        TextEntry::make('monto_preview')
                            ->label('Monto estimado')
                            ->state(fn (Get $get): string => 'S/ ' . number_format((float) $get('volumen_estimado') * (float) $get('precio_unitario_propuesto'), 2))
                            ->weight('bold')
                            ->color('success'),
                    ])
                    ->columns(2),

                Section::make('El lugar')
                    ->icon('heroicon-o-camera')
                    ->schema([
                        Select::make('viabilidad')->label('¿Es viable el vaciado?')->options(Contrato::VIABILIDADES)->required()->native(false)->live(),
                        Textarea::make('viabilidad_comentario')
                            ->label('Comentarios sobre la viabilidad')
                            ->placeholder('Ej: calle angosta, entrar con bomba; cables a 6 m...')
                            ->rows(2)
                            ->required(fn (Get $get): bool => in_array($get('viabilidad'), ['observaciones', 'no_viable'])),
                        Textarea::make('descripcion_lugar')->label('Descripción del lugar')->placeholder('Acceso, terreno, espacio para mixer o bomba...')->rows(3)->columnSpanFull(),
                        FileUpload::make('fotos_lugar')
                            ->label('Fotos del lugar')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->maxFiles(8)
                            ->maxSize(5120)
                            ->automaticallyResizeImagesMode('contain')
                            ->automaticallyResizeImagesToWidth('1600')
                            ->automaticallyResizeImagesToHeight('1600')
                            ->directory('cotizaciones/lugar')
                            ->panelLayout('grid')
                            ->columnSpanFull(),
                        Textarea::make('observaciones')->label('Observaciones para la oficina')->rows(2)->columnSpanFull(),
                    ])
                    ->columns(2),

                // El cliente separa su fecha con un adelanto; la oficina lo confirma al revisar
                Section::make('Adelanto del cliente')
                    ->icon('heroicon-o-banknotes')
                    ->description('Si el cliente te dejó un adelanto para separar su fecha, anótalo aquí. La oficina lo confirma y separa el horario.')
                    ->schema([
                        Toggle::make('dejo_adelanto')
                            ->label('El cliente dejó un adelanto')
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(fn (Toggle $component, ?Cotizacion $record) => $component->state((float) $record?->adelanto_declarado_monto > 0))
                            ->columnSpanFull(),
                        TextInput::make('adelanto_declarado_monto')
                            ->label('Monto del adelanto')
                            ->prefix('S/')
                            ->numeric()
                            ->minValue(1)
                            ->visible(fn (Get $get): bool => (bool) $get('dejo_adelanto'))
                            ->required(fn (Get $get): bool => (bool) $get('dejo_adelanto')),
                        Select::make('adelanto_declarado_forma_pago')
                            ->label('¿Cómo pagó?')
                            ->options(PagoPersonal::FORMAS_PAGO)
                            ->native(false)
                            ->visible(fn (Get $get): bool => (bool) $get('dejo_adelanto'))
                            ->required(fn (Get $get): bool => (bool) $get('dejo_adelanto')),
                        FileUpload::make('adelanto_voucher')
                            ->label('Foto del voucher (Yape, Plin o depósito)')
                            ->image()
                            ->maxSize(5120)
                            ->automaticallyResizeImagesMode('contain')
                            ->automaticallyResizeImagesToWidth('1400')
                            ->automaticallyResizeImagesToHeight('1400')
                            ->directory('cotizaciones/vouchers')
                            ->visible(fn (Get $get): bool => (bool) $get('dejo_adelanto'))
                            ->helperText('Si pagó en efectivo, puedes dejarlo vacío.')
                            ->columnSpanFull(),
                        TextEntry::make('aviso_fecha')
                            ->hiddenLabel()
                            ->state('Para separar el horario, completa la fecha y la hora tentativa en "Medición y vaciado".')
                            ->color('warning')
                            ->visible(fn (Get $get): bool => (bool) $get('dejo_adelanto') && (! $get('fecha_tentativa') || ! $get('hora_tentativa')))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')->label('N.°')->weight('bold')->searchable(),
                TextColumn::make('enviada_at')->label('Enviada')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('cliente_nombre')->label('Cliente')->searchable()->limit(30),
                TextColumn::make('volumen_estimado')->label('Volumen')->suffix(' m³'),
                TextColumn::make('estado')->label('Estado')->badge()
                    ->formatStateUsing(fn (string $state): string => Cotizacion::ESTADOS[$state] ?? $state)
                    ->color(fn (string $state): string => Cotizacion::ESTADO_COLORES[$state] ?? 'gray'),
            ])
            ->defaultSort('enviada_at', 'desc')
            ->filters([
                SelectFilter::make('estado')->label('Estado')->options(Cotizacion::ESTADOS),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->label('Corregir'),
            ])
            ->emptyStateHeading('Aún no registras cotizaciones')
            ->emptyStateDescription('Cuando visites una obra, toca "Nueva cotización" y regístrala desde tu celular.');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Estado')
                    ->schema([
                        TextEntry::make('codigo')->label('N.°')->weight('bold'),
                        TextEntry::make('estado')->label('Estado')->badge()
                            ->formatStateUsing(fn (string $state): string => Cotizacion::ESTADOS[$state] ?? $state)
                            ->color(fn (string $state): string => Cotizacion::ESTADO_COLORES[$state] ?? 'gray'),
                        TextEntry::make('enviada_at')->label('Enviada')->dateTime('d/m/Y H:i'),
                        TextEntry::make('adelanto_monto')->label('Adelanto confirmado')->money('PEN')->placeholder('—')
                            ->helperText(fn (Cotizacion $record): ?string => $record->estaSeparada()
                                ? 'Fecha separada: ' . $record->fecha_tentativa->format('d/m/Y') . ($record->horaTexto() ? " {$record->horaTexto()}" : '')
                                : null),
                        TextEntry::make('adelanto_declarado_monto')->label('Adelanto que anotaste')->money('PEN')
                            ->helperText(fn (Cotizacion $record): string => $record->tieneAdelantoPorConfirmar() ? 'La oficina aún no lo confirma' : 'Confirmado por la oficina')
                            ->color(fn (Cotizacion $record): string => $record->tieneAdelantoPorConfirmar() ? 'warning' : 'success')
                            ->visible(fn (Cotizacion $record): bool => (float) $record->adelanto_declarado_monto > 0),
                        TextEntry::make('motivo_cancelacion')->label('Cancelada')->color('danger')
                            ->helperText(fn (Cotizacion $record): ?string => $record->devolucion_at ? 'Se le devolvió el adelanto el ' . $record->devolucion_at->format('d/m/Y') : null)
                            ->visible(fn (Cotizacion $record): bool => $record->estado === 'cancelada'),
                        TextEntry::make('precio_unitario_aprobado')->label('Precio aprobado por m³')->money('PEN')->placeholder('Pendiente'),
                        TextEntry::make('motivo_rechazo')->label('Motivo del rechazo')->color('danger')
                            ->visible(fn (Cotizacion $record): bool => $record->estado === 'rechazada'),
                    ])
                    ->columns(['default' => 2, 'lg' => 3])
                    ->columnSpanFull(),

                Section::make('Ubicación')
                    ->schema([
                        ViewEntry::make('mapa')->hiddenLabel()->view('filament.infolists.mapa-obra'),
                        TextEntry::make('ubicacion_referencia')->label('Dirección y referencia'),
                    ])
                    ->columnSpanFull(),

                Section::make('Detalle')
                    ->schema([
                        TextEntry::make('cliente_nombre')->label('Cliente'),
                        TextEntry::make('cliente_telefono')->label('Teléfono')->placeholder('—'),
                        TextEntry::make('estructura')->label('Tipo de vaciado'),
                        TextEntry::make('tipo_concreto')->label('Concreto')->placeholder('—'),
                        TextEntry::make('volumen_estimado')->label('Volumen')->suffix(' m³'),
                        TextEntry::make('bombeo')->label('Bombeo')->placeholder('—'),
                        TextEntry::make('fecha_tentativa')->label('Fecha tentativa')->date('d/m/Y')->placeholder('—'),
                        TextEntry::make('precio_unitario_propuesto')->label('Precio propuesto por m³')->money('PEN'),
                        TextEntry::make('viabilidad')->label('Viabilidad')->badge()
                            ->formatStateUsing(fn (?string $state): string => Contrato::VIABILIDADES[$state] ?? '—')
                            ->color(fn (?string $state): string => Contrato::VIABILIDAD_COLORES[$state] ?? 'gray')
                            ->icon(fn (?string $state): ?string => Contrato::VIABILIDAD_ICONOS[$state] ?? null),
                        TextEntry::make('viabilidad_comentario')->label('Comentarios')->placeholder('—'),
                        TextEntry::make('descripcion_lugar')->label('Descripción del lugar')->placeholder('—')->columnSpanFull(),
                        ImageEntry::make('fotos_lugar')->label('Fotos')->disk('public')->imageHeight(140)->placeholder('Sin fotos')->columnSpanFull(),
                    ])
                    ->columns(['default' => 1, 'sm' => 2])
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListMisCotizaciones::route('/'),
            'create' => CreateMiCotizacion::route('/create'),
            'view'   => ViewMiCotizacion::route('/{record}'),
            'edit'   => EditMiCotizacion::route('/{record}/edit'),
        ];
    }
}
