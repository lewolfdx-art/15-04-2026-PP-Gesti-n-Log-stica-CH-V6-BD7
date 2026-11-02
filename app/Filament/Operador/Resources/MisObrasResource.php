<?php

namespace App\Filament\Operador\Resources;

use App\Filament\Operador\Resources\MisObrasResource\Pages\ListMisObras;
use App\Filament\Operador\Resources\MisObrasResource\Pages\ViewMiObra;
use App\Models\Contrato;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Obras asignadas al operador que inició sesión. Solo lectura y sin montos:
 * el operador ve dónde es, cómo llegar y qué esperar del lugar.
 */
class MisObrasResource extends Resource
{
    protected static ?string $model = Contrato::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Mis obras';
    protected static ?string $modelLabel = 'Obra';
    protected static ?string $pluralModelLabel = 'Mis obras';
    protected static ?string $slug = 'obras';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['operador', 'super_admin', 'admin']) ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $usuario = auth()->user();

        return parent::getEloquentQuery()
            ->whereHas('operadores', fn (Builder $query) => $query->where('trabajadores.user_id', $usuario?->getKey()));
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha de vaciado')
                    ->date('d/m/Y')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('ubicacion_referencia')
                    ->label('Dirección / referencia')
                    ->limit(60)
                    ->wrap()
                    ->placeholder('Sin dirección'),

                TextColumn::make('estructura')
                    ->label('Vaciado')
                    ->badge(),

                TextColumn::make('viabilidad')
                    ->label('Viabilidad')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Contrato::VIABILIDADES[$state] ?? '—')
                    ->color(fn (?string $state): string => Contrato::VIABILIDAD_COLORES[$state] ?? 'gray')
                    ->icon(fn (?string $state): ?string => Contrato::VIABILIDAD_ICONOS[$state] ?? null)
                    ->placeholder('Sin evaluar'),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                TernaryFilter::make('proximas')
                    ->label('Fecha')
                    ->placeholder('Todas')
                    ->trueLabel('Hoy y próximas')
                    ->falseLabel('Pasadas')
                    ->queries(
                        true: fn (Builder $query) => $query->whereDate('fecha', '>=', today()),
                        false: fn (Builder $query) => $query->whereDate('fecha', '<', today()),
                    ),
            ])
            ->recordUrl(fn (Contrato $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make()->label('Ver obra'),
                Action::make('como_llegar')
                    ->label('Cómo llegar')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Contrato $record): ?string => $record->urlComoLlegar(), shouldOpenInNewTab: true)
                    ->visible(fn (Contrato $record): bool => $record->tieneUbicacion()),
            ])
            ->emptyStateHeading('No tienes obras asignadas')
            ->emptyStateDescription('Cuando te asignen a un vaciado te llegará una notificación y aparecerá aquí.');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ubicación')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        ViewEntry::make('mapa')
                            ->hiddenLabel()
                            ->view('filament.infolists.mapa-obra'),

                        TextEntry::make('ubicacion_referencia')
                            ->label('Dirección / referencia')
                            ->placeholder('Sin dirección registrada'),
                    ])
                    ->columnSpanFull(),

                Section::make('Viabilidad del lugar')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        TextEntry::make('viabilidad')
                            ->label('¿Es viable?')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => Contrato::VIABILIDADES[$state] ?? '—')
                            ->color(fn (?string $state): string => Contrato::VIABILIDAD_COLORES[$state] ?? 'gray')
                            ->icon(fn (?string $state): ?string => Contrato::VIABILIDAD_ICONOS[$state] ?? null)
                            ->placeholder('Sin evaluar'),

                        TextEntry::make('viabilidad_comentario')
                            ->label('Comentarios')
                            ->placeholder('Sin comentarios'),

                        TextEntry::make('descripcion_lugar')
                            ->label('Descripción del lugar')
                            ->placeholder('Sin descripción')
                            ->columnSpanFull(),

                        ImageEntry::make('fotos_lugar')
                            ->label('Fotos del lugar')
                            ->disk('public')
                            ->imageHeight(160)
                            ->openUrlInNewTab()
                            ->url(fn ($state): ?string => is_string($state) ? asset('storage/' . $state) : null)
                            ->placeholder('Sin fotos')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Datos del vaciado')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        TextEntry::make('fecha')->label('Fecha de vaciado')->date('d/m/Y'),
                        TextEntry::make('estructura')->label('Tipo de vaciado')->placeholder('—'),
                        TextEntry::make('tipo_concreto')->label('Tipo de concreto')->placeholder('—'),
                        TextEntry::make('bombeo')->label('Bombeo')->placeholder('—'),
                        TextEntry::make('volumen_guia')->label('Volumen (m³)')->numeric(2)->placeholder('—'),
                        TextEntry::make('nombre_cliente')->label('Cliente')->placeholder('—'),
                        TextEntry::make('nombre_vendedor')->label('Asesor de ventas')->placeholder('—'),
                        TextEntry::make('operadores.nombre_completo')->label('Equipo asignado')->badge()->columnSpanFull(),
                    ])
                    ->columns(['default' => 1, 'sm' => 2, 'lg' => 3])
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMisObras::route('/'),
            'view'  => ViewMiObra::route('/{record}'),
        ];
    }
}
