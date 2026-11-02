<?php

namespace Tests\Feature;

use App\Models\Trabajador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cada rol entra solo donde le corresponde.
 */
class AccesoPorRolTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_sin_sesion_el_panel_lleva_al_login(): void
    {
        $this->get('/admin')->assertRedirect();
        $this->get('/admin/login')->assertOk();
    }

    public function test_el_administrador_abre_las_secciones_principales(): void
    {
        $admin = $this->usuarioConRol('super_admin');

        foreach (['/admin', '/admin/contratos', '/admin/cotizaciones', '/admin/programacion', '/admin/vehiculos',
            '/admin/pagos-personal', '/admin/trabajadores', '/admin/usuarios', '/admin/ingresos', '/admin/gastos',
            '/admin/libro-reclamaciones', '/admin/contratos/create'] as $ruta) {
            $this->actingAs($admin)->get($ruta)->assertOk();
        }
    }

    public function test_el_programador_solo_ve_programacion(): void
    {
        $programador = $this->usuarioConRol('coordinador_de_vaciados');

        $this->actingAs($programador)->get('/admin')->assertRedirect('/admin/programacion');
        $this->actingAs($programador)->get('/admin/programacion')->assertOk();

        foreach (['/admin/vehiculos', '/admin/contratos', '/admin/usuarios', '/admin/pagos-personal'] as $ruta) {
            $this->actingAs($programador)->get($ruta)->assertForbidden();
        }
    }

    public function test_operadores_y_asesores_van_a_su_panel(): void
    {
        $operador = $this->usuarioConRol('operador');
        $asesorUsuario = $this->usuarioConRol('asesor');
        $this->asesor('Asesor Con Acceso', ['user_id' => $asesorUsuario->id]);

        $this->actingAs($operador)->get('/admin/contratos')->assertRedirect();
        $this->actingAs($operador)->get('/operador/obras')->assertOk();
        $this->actingAs($operador)->get('/operador/cotizaciones')->assertForbidden();

        $this->actingAs($asesorUsuario)->get('/operador/cotizaciones')->assertOk();
        $this->actingAs($asesorUsuario)->get('/operador/cotizaciones/create')->assertOk();
        $this->actingAs($asesorUsuario)->get('/operador/obras')->assertForbidden();
    }

    public function test_el_cargo_de_programador_recibe_el_rol_correcto_al_dar_acceso(): void
    {
        $programador = Trabajador::create(['tipo_cargo' => Trabajador::CARGO_PROGRAMADOR, 'nombre_completo' => 'Arturo', 'activo' => true]);
        $mixer = Trabajador::create(['tipo_cargo' => 'operador_mixer', 'nombre_completo' => 'Pedro', 'activo' => true]);

        $this->assertSame('coordinador_de_vaciados', $programador->rolDeAcceso());
        $this->assertSame('operador', $mixer->rolDeAcceso());
        $this->assertFalse(Trabajador::paraObras()->whereKey($programador->id)->exists());
        $this->assertTrue(Trabajador::paraObras()->whereKey($mixer->id)->exists());
    }
}
