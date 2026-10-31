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

        // Panel de operadores: solo ven sus obras asignadas (los admins también pueden entrar a revisar)
        $tieneAcceso = $panel->getId() === 'operador'
            ? $this->hasAnyRole(['operador', 'super_admin', 'admin'])
            : $this->hasAnyRole(['super_admin', 'admin']);

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