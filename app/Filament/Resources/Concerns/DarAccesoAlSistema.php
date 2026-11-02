<?php

namespace App\Filament\Resources\Concerns;

use App\Models\Trabajador;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

/**
 * Acción "Dar acceso": crea el usuario de un trabajador con el rol indicado
 * (operador para ver sus obras, asesor para registrar cotizaciones). Entra por el botón Login de la web.
 */
trait DarAccesoAlSistema
{
    /**
     * @param  string|\Closure(Trabajador): string  $rol  rol fijo, o según el trabajador (p. ej. su cargo)
     * @param  string|\Closure(Trabajador): string  $descripcion
     */
    public static function accionDarAcceso(string | \Closure $rol, string | \Closure $descripcion): Action
    {
        return Action::make('dar_acceso')
            ->label('Dar acceso')
            ->icon('heroicon-o-key')
            ->color('success')
            ->visible(fn (Trabajador $record): bool => $record->user_id === null)
            ->modalHeading(fn (Trabajador $record): string => "Acceso al sistema para {$record->nombre_completo}")
            ->modalDescription($descripcion)
            ->schema([
                TextInput::make('email')
                    ->label('Correo (usuario)')
                    ->email()
                    ->required()
                    // Regla directa: unique() de Filament excluiría el id del trabajador de la fila
                    ->rule('unique:users,email'),

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->revealable()
                    ->required()
                    ->minLength(8),
            ])
            ->action(function (Trabajador $record, array $data) use ($rol): void {
                $usuario = User::create([
                    'name'      => $record->nombre_completo,
                    'email'     => $data['email'],
                    'password'  => $data['password'],
                    'is_active' => true,
                ]);
                $usuario->assignRole($rol instanceof \Closure ? $rol($record) : $rol);
                $record->update(['user_id' => $usuario->getKey()]);

                Notification::make()
                    ->title('Acceso creado')
                    ->body("{$record->nombre_completo} ya puede entrar con el botón Login de la web.")
                    ->success()
                    ->send();
            });
    }
}
