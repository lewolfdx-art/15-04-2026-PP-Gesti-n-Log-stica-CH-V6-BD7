<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LibroReclamacionResource\Pages\ListLibroReclamaciones;
use App\Filament\Resources\LibroReclamacionResource\Pages\ViewLibroReclamacion;
use App\Mail\RespuestaReclamacionMail;
use App\Models\LibroReclamacion;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Bandeja del Libro de Reclamaciones virtual. Las hojas llegan desde la web pública
 * y por norma no se modifican ni se eliminan: solo se responden dentro del plazo legal.
 */
class LibroReclamacionResource extends Resource
{
    protected static ?string $model = LibroReclamacion::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Comercial';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Libro de reclamaciones';
    protected static ?string $modelLabel = 'Hoja de reclamación';
    protected static ?string $pluralModelLabel = 'Libro de reclamaciones';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'libro-reclamaciones';

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

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $pendientes = LibroReclamacion::pending()->count();

        return $pendientes > 0 ? (string) $pendientes : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return LibroReclamacion::pending()->whereDate('fecha_limite', '<=', today()->addWeekdays(3))->exists()
            ? 'danger'
            : 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Hojas pendientes de respuesta';
    }

    /**
     * Acción para responder: guarda la respuesta y la envía al correo del consumidor
     */
    public static function accionResponder(): Action
    {
        return Action::make('responder')
            ->label('Responder')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->visible(fn (LibroReclamacion $record): bool => ! $record->estaRespondido())
            ->modalHeading(fn (LibroReclamacion $record): string => "Responder {$record->tipo} {$record->codigo}")
            ->modalDescription(fn (LibroReclamacion $record): string => "La respuesta se enviará a {$record->email} y no podrá modificarse.")
            ->schema([
                Textarea::make('respuesta')
                    ->label('Respuesta al consumidor')
                    ->required()
                    ->minLength(20)
                    ->rows(8),
            ])
            ->action(function (LibroReclamacion $record, array $data): void {
                $record->responder($data['respuesta'], auth()->user());

                try {
                    Mail::to($record->email)->send(new RespuestaReclamacionMail($record));
                    $enviado = true;
                } catch (Throwable $e) {
                    Log::error("No se pudo enviar la respuesta de {$record->codigo}: {$e->getMessage()}");
                    $enviado = false;
                }

                Notification::make()
                    ->title("Respuesta registrada para {$record->codigo}")
                    ->body($enviado ? "Se envió a {$record->email}." : 'Quedó guardada, pero el correo no se pudo enviar. Revisa la configuración de correo.')
                    ->color($enviado ? 'success' : 'warning')
                    ->icon($enviado ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle')
                    ->send();
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('N.° hoja')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => LibroReclamacion::TIPOS[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => $state === 'queja' ? 'warning' : 'danger'),

                TextColumn::make('claimant_name')
                    ->label('Consumidor')
                    ->searchable()
                    ->description(fn (LibroReclamacion $record): string => "{$record->document_type} {$record->document_number}"),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => LibroReclamacion::ESTADOS[$state] ?? (string) $state)
                    ->color(fn (?string $state): string => $state === 'resolved' ? 'success' : 'gray'),

                TextColumn::make('fecha_limite')
                    ->label('Responder hasta')
                    ->date('d/m/Y')
                    ->sortable()
                    ->badge()
                    ->color(function (LibroReclamacion $record): string {
                        $dias = $record->diasHabilesRestantes();

                        return match (true) {
                            $dias === null => 'gray',
                            $dias < 0      => 'danger',
                            $dias <= 3     => 'warning',
                            default        => 'success',
                        };
                    })
                    ->description(function (LibroReclamacion $record): ?string {
                        $dias = $record->diasHabilesRestantes();

                        return match (true) {
                            $dias === null => null,
                            $dias < 0      => 'Vencido hace ' . abs($dias) . ' día(s) hábil(es)',
                            $dias === 0    => 'Vence hoy',
                            default        => "Quedan {$dias} día(s) hábil(es)",
                        };
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(LibroReclamacion::ESTADOS),

                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options(LibroReclamacion::TIPOS),
            ])
            ->recordActions([
                static::accionResponder(),
                ViewAction::make(),
            ])
            ->emptyStateHeading('Sin reclamos registrados')
            ->emptyStateDescription('Las hojas que los clientes envíen desde la web aparecerán aquí.');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hoja de reclamación')
                    ->schema([
                        TextEntry::make('codigo')->label('N.° hoja')->weight('bold'),
                        TextEntry::make('created_at')->label('Fecha de registro')->dateTime('d/m/Y H:i'),
                        TextEntry::make('tipo')->label('Tipo')->badge()
                            ->formatStateUsing(fn (?string $state): string => LibroReclamacion::TIPOS[$state] ?? (string) $state)
                            ->color(fn (?string $state): string => $state === 'queja' ? 'warning' : 'danger'),
                        TextEntry::make('fecha_limite')->label('Responder hasta')->date('d/m/Y'),
                    ])
                    ->columns(['default' => 2, 'lg' => 4])
                    ->columnSpanFull(),

                Section::make('1. Identificación del consumidor')
                    ->schema([
                        TextEntry::make('claimant_name')->label('Nombre'),
                        TextEntry::make('document_number')->label('Documento')
                            ->formatStateUsing(fn (LibroReclamacion $record): string => "{$record->document_type} {$record->document_number}"),
                        TextEntry::make('phone')->label('Teléfono'),
                        TextEntry::make('email')->label('Correo')->copyable(),
                        TextEntry::make('address')->label('Domicilio')->columnSpanFull(),
                        TextEntry::make('apoderado_nombre')->label('Padre, madre o apoderado')
                            ->visible(fn (LibroReclamacion $record): bool => $record->menor_edad),
                    ])
                    ->columns(2),

                Section::make('2. Identificación del bien contratado')
                    ->schema([
                        TextEntry::make('bien_tipo')->label('Tipo')
                            ->formatStateUsing(fn (?string $state): string => LibroReclamacion::BIENES[$state] ?? '—'),
                        TextEntry::make('monto')->label('Monto reclamado')->money('PEN')->placeholder('—'),
                        TextEntry::make('order_number')->label('N.° contrato / pedido')->placeholder('—'),
                        TextEntry::make('bien_descripcion')->label('Descripción')->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('3. Detalle')
                    ->schema([
                        TextEntry::make('claim_description')->label('Detalle del reclamo o queja'),
                        TextEntry::make('petition')->label('Pedido del consumidor'),
                    ])
                    ->columnSpanFull(),

                Section::make('Respuesta de la empresa')
                    ->schema([
                        TextEntry::make('response')->label('Respuesta')->placeholder('Pendiente de respuesta')->columnSpanFull(),
                        TextEntry::make('respondido_at')->label('Respondido el')->dateTime('d/m/Y H:i')->placeholder('—'),
                        TextEntry::make('respondidoPor.name')->label('Respondido por')->placeholder('—'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLibroReclamaciones::route('/'),
            'view'  => ViewLibroReclamacion::route('/{record}'),
        ];
    }
}
