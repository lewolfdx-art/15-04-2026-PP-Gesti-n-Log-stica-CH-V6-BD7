<?php

namespace Tests\Feature;

use App\Models\Contrato;
use App\Models\Cotizacion;
use App\Models\Ingreso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Adelanto del cliente: el asesor lo anota, la oficina lo confirma (fecha separada),
 * se puede cancelar con devolución y, al pasar a contrato, entra a Ingresos.
 */
class CotizacionAdelantoTest extends TestCase
{
    use RefreshDatabase;

    private function cotizacion(array $datos = []): Cotizacion
    {
        $asesor = $this->asesor();

        return Cotizacion::create(array_merge([
            'asesor_id'                 => $asesor->id,
            'cliente_nombre'            => 'Juan Pérez',
            'ubicacion_referencia'      => 'El Tambo',
            'estructura'                => 'LOSA ALIGERADA',
            'volumen_estimado'          => 20,
            'precio_unitario_propuesto' => 300,
            'fecha_tentativa'           => today()->addDays(5)->toDateString(),
            'hora_tentativa'            => '08:00',
        ], $datos));
    }

    public function test_el_adelanto_que_anota_el_asesor_queda_por_confirmar(): void
    {
        $cotizacion = $this->cotizacion(['adelanto_declarado_monto' => 300, 'adelanto_declarado_forma_pago' => 'YAPE_PLIN']);

        $this->assertTrue($cotizacion->tieneAdelantoPorConfirmar());
        $this->assertNotNull($cotizacion->adelanto_declarado_at);
        $this->assertSame('enviada', $cotizacion->estado);
        $this->assertFalse($cotizacion->estaSeparada());
    }

    public function test_confirmar_el_adelanto_separa_la_fecha_sin_tocar_ingresos(): void
    {
        $cotizacion = $this->cotizacion(['adelanto_declarado_monto' => 300]);

        $cotizacion->registrarAdelanto(300, 'YAPE_PLIN');

        $this->assertSame('en_espera', $cotizacion->estado);
        $this->assertTrue($cotizacion->estaSeparada());
        $this->assertSame(1, Cotizacion::separadas()->count());
        $this->assertSame(0, Ingreso::count());
    }

    public function test_avisa_si_otra_fecha_separada_se_cruza_el_mismo_dia(): void
    {
        $primera = $this->cotizacion(['hora_tentativa' => '09:00']);
        $primera->registrarAdelanto(200, 'EFECTIVO');

        $cerca = $this->cotizacion(['cliente_nombre' => 'Cerca', 'hora_tentativa' => '08:00']);
        $lejos = $this->cotizacion(['cliente_nombre' => 'Lejos', 'hora_tentativa' => '15:00']);

        $this->assertCount(1, $cerca->cruces());
        $this->assertSame([], $lejos->cruces());
    }

    public function test_cancelar_libera_la_fecha_y_registra_la_devolucion(): void
    {
        $cotizacion = $this->cotizacion();
        $cotizacion->registrarAdelanto(300, 'EFECTIVO');

        $cotizacion->cancelarConDevolucion('Postergó la obra', 'EFECTIVO');

        $this->assertSame('cancelada', $cotizacion->estado);
        $this->assertNotNull($cotizacion->devolucion_at);
        $this->assertSame(0, Cotizacion::separadas()->count());
        $this->assertSame(0, Ingreso::count());
    }

    public function test_solo_se_cancela_una_cotizacion_con_fecha_separada(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cotizacion()->cancelarConDevolucion('No hay adelanto', 'EFECTIVO');
    }

    public function test_al_pasar_a_contrato_el_adelanto_entra_a_ingresos_ligado_al_contrato(): void
    {
        $cotizacion = $this->cotizacion();
        $cotizacion->registrarAdelanto(300, 'YAPE_PLIN');

        $contrato = $cotizacion->convertirEnContrato(['precio_unitario_aprobado' => 290, 'numero_contrato' => '950']);

        $this->assertSame('C-950', $contrato->numero_contrato);
        $this->assertSame('convertida', $cotizacion->fresh()->estado);
        $this->assertEquals(5800, $contrato->monto_total);
        $this->assertSame(300.0, $contrato->totalCobrado());
        $this->assertSame(5500.0, $contrato->saldoPorCobrar());
        $this->assertSame('ADEL-CONTRATO', Ingreso::sole()->modo_pago);
    }

    public function test_una_cotizacion_convertida_ya_no_se_puede_modificar(): void
    {
        $cotizacion = $this->cotizacion();
        $cotizacion->convertirEnContrato(['numero_contrato' => '951']);

        $this->expectException(InvalidArgumentException::class);
        $cotizacion->registrarAdelanto(100, 'EFECTIVO');
    }

    public function test_el_contrato_creado_desde_la_cotizacion_queda_ligado_al_asesor(): void
    {
        $cotizacion = $this->cotizacion();

        $contrato = $cotizacion->convertirEnContrato(['numero_contrato' => '952']);

        $this->assertSame($cotizacion->asesor_id, $contrato->vendedor_id);
        $this->assertInstanceOf(Contrato::class, $cotizacion->fresh()->contrato);
    }
}
