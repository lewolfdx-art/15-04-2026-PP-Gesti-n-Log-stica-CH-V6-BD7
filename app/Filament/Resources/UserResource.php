<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Gestión de Usuarios';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Usuario')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Cuenta Activa')
                            ->default(true)
                            ->helperText('Si está desactivado, el usuario no podrá iniciar sesión')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context) => $context === 'create')
                            ->helperText(fn (string $context) => 
                                $context === 'edit' ? 'Dejar en blanco para mantener la contraseña actual' : ''
                            ),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->same('password')
                            ->required(fn (string $context) => $context === 'create'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Roles y Permisos')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Asignar Roles')
                            ->multiple()
                            ->preload()
                            ->relationship('roles', 'name')
                            ->helperText('Solo "super_admin" y "admin" pueden acceder al panel.'),
                    ]),

                Forms\Components\Section::make('Información de Sesión')
                    ->schema([
                        Forms\Components\Placeholder::make('last_login_at')
                            ->label('Último acceso')
                            ->content(fn ($record) => $record?->last_login_at ? $record->last_login_at->format('d/m/Y H:i:s') : 'Nunca'),

                        Forms\Components\Placeholder::make('last_login_ip')
                            ->label('Última IP')
                            ->content(fn ($record) => $record?->last_login_ip ?? 'N/A'),
                    ])
                    ->visible(fn ($record) => $record !== null)
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Correo copiado'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'coordinador_de_vaciados' => 'info',
                        default => 'gray',
                    })
                    ->separator(', '),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Último acceso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Estado de cuenta')
                    ->options([
                        '1' => 'Activos',
                        '0' => 'Inactivos',
                    ]),
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                // Orden: Desactivar -> Resetear contraseña -> Editar -> Borrar
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn ($record): string => $record->is_active ? 'Desactivar' : 'Activar')
                    ->icon(fn ($record): string => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check-circle')
                    ->color(fn ($record): string => $record->is_active ? 'danger' : 'success')
                    ->action(function ($record): void {
                        if ($record->is_active) {
                            $record->deactivate();
                            Notification::make()
                                ->title('Usuario Desactivado')
                                ->body("{$record->name} ha sido desactivado y no podrá iniciar sesión")
                                ->warning()
                                ->send();
                        } else {
                            $record->activate();
                            Notification::make()
                                ->title('Usuario Activado')
                                ->body("{$record->name} ha sido activado y puede iniciar sesión nuevamente")
                                ->success()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record): string => $record->is_active ? 'Desactivar usuario' : 'Activar usuario')
                    ->modalDescription(fn ($record): string => $record->is_active 
                        ? '¿Estás seguro de desactivar este usuario? No podrá acceder al sistema.' 
                        : '¿Estás seguro de activar este usuario? Podrá acceder al sistema nuevamente.')
                    ->visible(fn ($record): bool => !$record->hasRole('super_admin') || self::getAuthUser()->hasRole('super_admin')),

                Tables\Actions\Action::make('reset_password')
                    ->label('')
                    ->tooltip('Resetear contraseña')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->action(function ($record): void {
                        $newPassword = Str::random(8);
                        $record->password = bcrypt($newPassword);
                        $record->save();

                        Notification::make()
                            ->title('Contraseña restablecida')
                            ->body("Nueva contraseña para {$record->name}: {$newPassword}")
                            ->persistent()
                            ->warning()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Restablecer contraseña')
                    ->modalDescription('Se generará una nueva contraseña aleatoria de 8 caracteres.')
                    ->visible(fn ($record): bool => self::getAuthUser()?->hasRole('super_admin') ?? false),

                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Editar')
                    ->color('primary'),

                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip('Borrar')
                    ->visible(fn ($record): bool => !$record->hasRole('super_admin') || self::getAuthUser()->hasRole('super_admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (): bool => self::getAuthUser()?->hasRole('super_admin') ?? false),
                    
                    Tables\Actions\BulkAction::make('bulk_activate')
                        ->label('Activar seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->activate())
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    
                    Tables\Actions\BulkAction::make('bulk_deactivate')
                        ->label('Desactivar seleccionados')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->deactivate())
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    // ====================== PERMISOS ======================

    /**
     * Obtener el usuario autenticado
     */
    private static function getAuthUser(): ?User
    {
        $user = Auth::user();
        return $user instanceof User ? $user : null;
    }

    public static function canViewAny(): bool
    {
        $user = self::getAuthUser();
        if (!$user) return false;
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    public static function canCreate(): bool
    {
        $user = self::getAuthUser();
        if (!$user) return false;
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    public static function canEdit($record): bool
    {
        $user = self::getAuthUser();
        if (!$user) return false;
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    public static function canDelete($record): bool
    {
        $user = self::getAuthUser();
        if (!$user) return false;

        if ($record->hasRole('super_admin') && !$user->hasRole('super_admin')) {
            return false;
        }

        return $user->hasAnyRole(['super_admin', 'admin']);
    }
}