<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    /** Nombres de los roles para mostrar en el panel */
    public const ETIQUETAS_ROL = [
        'super_admin'             => 'Super administrador',
        'admin'                   => 'Administrador',
        'coordinador_de_vaciados' => 'Programador de vaciados',
        'asesor'                  => 'Asesor de ventas',
        'operador'                => 'Operador (sus obras)',
    ];

    protected $fillable = [
        'name',
        'email',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Control de acceso al Panel de Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->id) {
            return false;
        }

        // Verificar si el usuario está activo
        if (!$this->is_active) {
            Log::warning("Acceso denegado - Cuenta inactiva: {$this->email}");
            return false;
        }

        $roles = $this->getRoleNames();

        // Panel de campo (/operador): operadores ven sus obras y asesores registran cotizaciones.
        // Panel /admin: administradores, y el programador de vaciados solo con acceso a la Programación.
        $tieneAcceso = $panel->getId() === 'operador'
            ? $this->hasAnyRole(['operador', 'asesor', 'super_admin', 'admin'])
            : $this->hasAnyRole(['super_admin', 'admin', 'coordinador_de_vaciados']);

        Log::info("Acceso Filament - Panel: /{$panel->getPath()} | Email: {$this->email} | Activo: {$this->is_active} | Roles: {$roles->join(', ')} | Acceso: " . ($tieneAcceso ? 'SI' : 'NO'));

        return $tieneAcceso;
    }

    /**
     * Ficha de trabajador vinculada (para los usuarios operadores)
     */
    public function trabajador(): HasOne
    {
        return $this->hasOne(Trabajador::class);
    }

    /**
     * Activar usuario
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
        Log::info("Usuario activado: {$this->email}");
    }

    /**
     * Desactivar usuario
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
        Log::info("Usuario desactivado: {$this->email}");
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}