<?php

namespace Tests;

use App\Models\Contrato;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    /**
     * Candado: las pruebas borran y recrean la base en cada test (RefreshDatabase).
     * Solo se permite la base SQLite en memoria de phpunit.xml; si la configuración apunta a otra
     * (por ejemplo, un config:cache con MySQL), se detiene ANTES de tocar nada.
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        $conexion = config('database.default');
        $base = config("database.connections.{$conexion}.database");

        if ($conexion !== 'sqlite' || $base !== ':memory:') {
            throw new RuntimeException(
                "Pruebas detenidas: usan la base \"{$conexion}:{$base}\" en lugar de SQLite en memoria. "
                . 'Ejecuta "php artisan config:clear" y vuelve a correrlas.'
            );
        }

        return $app;
    }

    protected function crearRoles(): void
    {
        foreach (['super_admin', 'admin', 'coordinador_de_vaciados', 'operador', 'asesor'] as $rol) {
            Role::findOrCreate($rol, 'web');
        }
    }

    protected function usuarioConRol(string $rol, array $datos = []): User
    {
        $this->crearRoles();

        $usuario = User::factory()->create(array_merge(['is_active' => true], $datos));
        $usuario->assignRole($rol);

        return $usuario;
    }

    protected function asesor(string $nombre = 'Andrea Asesora', array $datos = []): Trabajador
    {
        // Los datos de cada prueba reemplazan a los valores por defecto
        return Trabajador::create(array_merge([
            'tipo_cargo'      => Trabajador::CARGO_ASESOR,
            'nombre_completo' => $nombre,
            'activo'          => true,
        ], $datos));
    }

    protected function contrato(array $datos = []): Contrato
    {
        return Contrato::create(array_merge([
            'fecha'           => today()->toDateString(),
            'numero_contrato' => 'C-' . random_int(1000, 99999),
            'nombre_cliente'  => 'Cliente de prueba',
            'estructura'      => 'LOSA ALIGERADA',
            'tipo_concreto'   => '210',
            'volumen_guia'    => 10,
            'volumen_real'    => 10,
            'pu'              => 300,
            'monto_total'     => 3000,
            'venta_neta'      => 3000,
            'estado_pago'     => 'DEBE',
        ], $datos));
    }
}
