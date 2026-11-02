<?php

namespace Tests\Feature;

use App\Models\Contrato;
use App\Models\PagoPersonal;
use App\Models\Trabajador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Comisiones de los asesores: contratos ligados por vendedor_id, pendientes de quincenas anteriores,
 * liquidación por obra y sin pagar dos veces.
 */
class ComisionesAsesorTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_contrato_se_liga_al_asesor_y_lo_sigue_si_cambia_de_nombre(): void
    {
        $asesor = $this->asesor('Camila Rojas');
        $contrato = $this->contrato(['nombre_vendedor' => 'Camila Rojas']);

        $this->assertSame($asesor->id, $contrato->vendedor_id);

        $asesor->update(['nombre_completo' => 'Camila Rojas Pérez']);

        $this->assertSame('Camila Rojas Pérez', $contrato->fresh()->nombre_vendedor);
    }

    public function test_incluye_comisiones_pendientes_de_quincenas_anteriores(): void
    {
        $asesor = $this->asesor('Andrew Vega');
        $this->contrato(['nombre_vendedor' => 'Andrew Vega', 'fecha' => '2026-04-02', 'comision' => 1500]);
        $this->contrato(['nombre_vendedor' => 'Andrew Vega', 'fecha' => '2026-04-15', 'comision' => 725]);

        $this->assertEquals(2225, PagoPersonal::montoBase($asesor, 2026, 11, 1));
    }

    public function test_liquidar_solo_una_obra_deja_la_otra_pendiente_y_no_se_paga_dos_veces(): void
    {
        $asesor = $this->asesor('Andrew Vega');
        $c1 = $this->contrato(['nombre_vendedor' => 'Andrew Vega', 'fecha' => '2026-04-02', 'comision' => 1500]);
        $c2 = $this->contrato(['nombre_vendedor' => 'Andrew Vega', 'fecha' => '2026-04-15', 'comision' => 725]);
        $datos = ['trabajador_id' => $asesor->id, 'anio' => 2026, 'mes' => 11, 'quincena' => 1];

        $pago = PagoPersonal::registrar($datos + ['contratos_ids' => [$c1->id]]);

        $this->assertEquals(1500, $pago->monto_base);
        $this->assertSame($pago->id, $c1->fresh()->pago_comision_id);
        $this->assertNull($c2->fresh()->pago_comision_id);

        // Segundo pago en la misma quincena: solo queda la obra 2
        $segundo = PagoPersonal::registrar($datos);
        $this->assertEquals(725, $segundo->monto_base);

        // Ya no queda nada pendiente: sin monto a mano se rechaza
        $this->expectException(InvalidArgumentException::class);
        PagoPersonal::registrar($datos + ['contratos_ids' => [$c1->id]]);
    }

    public function test_sin_contratos_pendientes_se_acepta_un_monto_a_mano(): void
    {
        $asesor = $this->asesor('Nuevo Asesor');

        $pago = PagoPersonal::registrar(['trabajador_id' => $asesor->id, 'anio' => 2026, 'mes' => 11, 'quincena' => 1, 'monto_base' => 250, 'adicionales' => 50]);

        $this->assertEquals(300, $pago->total);
    }

    public function test_el_sueldo_se_paga_una_sola_vez_por_quincena(): void
    {
        $operador = Trabajador::create(['tipo_cargo' => 'operador_mixer', 'nombre_completo' => 'Jordan', 'sueldo_mensual' => 2400, 'activo' => true]);
        $datos = ['trabajador_id' => $operador->id, 'anio' => 2026, 'mes' => 10, 'quincena' => 2];

        $pago = PagoPersonal::registrar($datos);
        $this->assertEquals(1200, $pago->monto_base);

        $this->expectException(InvalidArgumentException::class);
        PagoPersonal::registrar($datos);
    }

    public function test_la_comision_negativa_no_se_paga(): void
    {
        $asesor = $this->asesor('Asesor X');
        $this->contrato(['nombre_vendedor' => 'Asesor X', 'comision' => -500]);

        $this->assertEquals(0, PagoPersonal::montoBase($asesor, 2026, 11, 2));
        $this->assertSame(0, Contrato::where('comision', '>', 0)->count());
    }
}
