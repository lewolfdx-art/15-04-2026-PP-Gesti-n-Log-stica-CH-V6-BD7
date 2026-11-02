<?php

namespace Tests\Feature;

use App\Filament\Operador\Resources\MisCotizacionesResource\Pages\CreateMiCotizacion;
use App\Filament\Resources\ContratoResource\Pages\ViewContrato;
use App\Filament\Resources\CotizacionResource\Pages\ViewCotizacion;
use App\Models\Contrato;
use App\Models\Cotizacion;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * El flujo completo por las pantallas: el asesor envía la cotización con adelanto, la oficina lo confirma
 * (fecha separada), la pasa a contrato y registra los pagos hasta dejarlo PAGADO.
 */
class FlujoCotizacionPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_de_la_cotizacion_con_adelanto_al_contrato_pagado(): void
    {
        $admin = $this->usuarioConRol('super_admin');
        $programador = $this->usuarioConRol('coordinador_de_vaciados');
        $asesorUsuario = $this->usuarioConRol('asesor');
        $this->asesor('Andrew Vega', ['user_id' => $asesorUsuario->id]);
        $fecha = today()->addDays(6)->toDateString();

        // 1) El asesor envía la cotización desde su celular, con adelanto de S/ 300
        $this->actingAs($asesorUsuario);
        Filament::setCurrentPanel(Filament::getPanel('operador'));
        Livewire::test(CreateMiCotizacion::class)
            ->set('data.cliente_nombre', 'Cliente Adelanto')
            ->set('data.ubicacion_referencia', 'El Tambo, Jr. Prueba 123')
            ->set('data.estructura', 'LOSA ALIGERADA')
            ->set('data.volumen_estimado', 20)
            ->set('data.precio_unitario_propuesto', 300)
            ->set('data.fecha_tentativa', $fecha)
            ->set('data.hora_tentativa', '08:00')
            ->set('data.viabilidad', 'viable')
            ->set('data.dejo_adelanto', true)
            ->set('data.adelanto_declarado_monto', 300)
            ->set('data.adelanto_declarado_forma_pago', 'YAPE_PLIN')
            ->call('create')
            ->assertHasNoErrors();

        $cotizacion = Cotizacion::sole();
        $this->assertTrue($cotizacion->tieneAdelantoPorConfirmar());
        $this->assertSame(1, $admin->notifications()->count(), 'La oficina recibe el aviso');

        // 2) La oficina confirma el adelanto: la fecha queda separada y se avisa al programador
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Livewire::test(ViewCotizacion::class, ['record' => $cotizacion->id])
            ->mountAction('registrar_adelanto')
            ->set('mountedActions.0.data.monto', 300)
            ->set('mountedActions.0.data.forma_pago', 'YAPE_PLIN')
            ->set('mountedActions.0.data.fecha_tentativa', $fecha)
            ->set('mountedActions.0.data.hora_tentativa', '08:00')
            ->callMountedAction();

        $this->assertTrue($cotizacion->fresh()->estaSeparada());
        $this->assertSame(1, $programador->notifications()->count(), 'El programador recibe "Fecha separada"');

        // 3) Pasa a contrato con su número
        Livewire::test(ViewCotizacion::class, ['record' => $cotizacion->id])
            ->mountAction('pasar_a_contrato')
            ->set('mountedActions.0.data.precio_unitario_aprobado', 300)
            ->set('mountedActions.0.data.fecha', $fecha)
            ->set('mountedActions.0.data.numero_contrato', '950')
            ->callMountedAction();

        $contrato = Contrato::where('numero_contrato', 'C-950')->sole();
        $this->assertSame(300.0, $contrato->totalCobrado());
        $this->assertSame(5700.0, $contrato->saldoPorCobrar());

        // 4) Registra el pago del saldo desde el contrato: queda PAGADO
        Livewire::test(ViewContrato::class, ['record' => $contrato->id])
            ->mountAction('registrar_pago')
            ->set('mountedActions.0.data.monto', 5700)
            ->set('mountedActions.0.data.fecha', today()->toDateString())
            ->set('mountedActions.0.data.forma_pago', 'EFECTIVO')
            ->callMountedAction();

        $contrato->refresh();
        $this->assertSame('PAGADO', $contrato->estado_pago);
        $this->assertSame(0.0, $contrato->saldoPorCobrar());
    }

    public function test_al_pasar_a_contrato_el_numero_es_obligatorio_y_unico(): void
    {
        $this->actingAs($this->usuarioConRol('super_admin'));
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->contrato(['numero_contrato' => 'C-777']);
        $cotizacion = Cotizacion::create(['asesor_id' => $this->asesor()->id, 'cliente_nombre' => 'X', 'precio_unitario_propuesto' => 300]);

        Livewire::test(ViewCotizacion::class, ['record' => $cotizacion->id])
            ->mountAction('pasar_a_contrato')
            ->set('mountedActions.0.data.numero_contrato', '777')
            ->set('mountedActions.0.data.fecha', today()->toDateString())
            ->callMountedAction()
            ->assertHasActionErrors(['numero_contrato']);

        $this->assertSame('enviada', $cotizacion->fresh()->estado);
    }
}
