<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Usuarios';
    //protected static ?int $navigationSort = 10;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Usuario')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Toggle::make('is_active')
                            ->label('Cuenta Activa')
                            ->default(true)
                            ->helperText('Si está desactivado, el usuario no podrá iniciar sesión')
                            ->columnSpanFull(),

                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => !empty($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context) => $context === 'create')
                            ->helperText(fn (string $context) => 
                                $context === 'edit' ? 'Dejar en blanco para mantener la contraseña actual' : ''
                            ),

                        TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->same('password')
                            ->required(fn (string $context) => $context === 'create'),
                    ])
                    ->columns(2),

                Section::make('Roles y Permisos')
                    ->schema([
                        Select::make('roles')
                            ->label('Asignar Roles')
                            ->multiple()
                            ->preload()
                            ->relationship('roles', 'name')
                            ->helperText('Solo "super_admin" y "admin" pueden acceder al panel.'),
                    ]),

                Section::make('Información de Sesión')
                    ->schema([
                        Placeholder::make('last_login_at')
                            ->label('Último acceso')
                            ->content(fn ($record) => $record?->last_login_at ? $record->last_login_at->format('d/m/Y H:i:s') : 'Nunca'),

                        Placeholder::make('last_login_ip')
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
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Correo copiado'),

                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'coordinador_de_vaciados' => 'info',
                        default => 'gray',
                    })
                    ->separator(', '),

                TextColumn::make('last_login_at')
                    ->label('Último acceso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Estado de cuenta')
                    ->options([
                        '1' => 'Activos',
                        '0' => 'Inactivos',
                    ]),
                SelectFilter::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                // Orden: Desactivar -> Resetear contraseña -> Editar -> Borrar
                Action::make('toggle_active')
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

                Action::make('reset_password')
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

                EditAction::make()
                    ->label('')
                    ->tooltip('Editar')
                    ->color('primary'),

                DeleteAction::make()
                    ->label('')
                    ->tooltip('Borrar')
                    ->visible(fn ($record): bool => !$record->hasRole('super_admin') || self::getAuthUser()->hasRole('super_admin')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => self::getAuthUser()?->hasRole('super_admin') ?? false),
                    
                    BulkAction::make('bulk_activate')
                        ->label('Activar seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->activate())
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                    
                    BulkAction::make('bulk_deactivate')
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
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