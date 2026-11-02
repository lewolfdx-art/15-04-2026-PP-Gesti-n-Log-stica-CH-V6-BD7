<?php

namespace Tests\Feature;

use App\Models\Ingreso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Cobranza del contrato: pagado, saldo, abonos y paso automático a PAGADO.
 */
class ContratoPagosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_saldo_descuenta_los_ingresos_del_contrato_con_o_sin_prefijo(): void
    {
        $contrato = $this->contrato(['numero_contrato' => 'C-901', 'monto_total' => 7750]);

        // Uno anotado como "C-901" y otro escrito a mano como "901"
        Ingreso::create(['fecha' => today(), 'modo_pago' => 'ADEL-CONTRATO', 'numero_contrato' => 'C-901', 'monto' => 300]);
        Ingreso::create(['fecha' => today(), 'modo_pago' => 'ADEL-CONTRATO', 'numero_contrato' => '901', 'monto' => 700]);

        $this->assertSame(1000.0, $contrato->totalCobrado());
        $this->assertSame(6750.0, $contrato->saldoPorCobrar());
    }

    public function test_un_abono_parcial_deja_saldo_y_el_ultimo_pago_marca_pagado(): void
    {
        $contrato = $this->contrato(['monto_total' => 6000]);

        $contrato->registrarPago(2000, today()->toDateString(), 'Transferencia');
        $this->assertSame(4000.0, $contrato->fresh()->saldoPorCobrar());
        $this->assertSame('DEBE', $contrato->fresh()->estado_pago);

        $contrato->registrarPago(4000, today()->toDateString(), 'Efectivo');
        $contrato->refresh();
        $this->assertSame(0.0, $contrato->saldoPorCobrar());
        $this->assertSame('PAGADO', $contrato->estado_pago);

        // Abono + cancelación; marcar PAGADO no agrega un tercer ingreso
        $this->assertCount(2, $contrato->pagos());
        $this->assertSame(['ADEL-CONTRATO', 'CAN-CONTRATO'], $contrato->pagos()->pluck('modo_pago')->all());
    }

    public function test_no_acepta_un_pago_mayor_al_saldo(): void
    {
        $contrato = $this->contrato(['monto_total' => 1000]);

        $this->expectException(InvalidArgumentException::class);
        $contrato->registrarPago(1500, today()->toDateString());
    }

    public function test_no_acepta_un_pago_de_cero(): void
    {
        $contrato = $this->contrato(['monto_total' => 1000]);

        $this->expectException(InvalidArgumentException::class);
        $contrato->registrarPago(0, today()->toDateString());
    }

    public function test_marcar_pagado_registra_solo_lo_que_faltaba(): void
    {
        $contrato = $this->contrato(['numero_contrato' => 'C-500', 'monto_total' => 5000]);
        Ingreso::create(['fecha' => today(), 'modo_pago' => 'ADEL-CONTRATO', 'numero_contrato' => 'C-500', 'monto' => 1200]);

        $contrato->update(['estado_pago' => 'PAGADO']);

        $cancelacion = Ingreso::where('numero_contrato', 'C-500')->where('modo_pago', 'CAN-CONTRATO')->sole();
        $this->assertEquals(3800, $cancelacion->monto);
        $this->assertSame(0.0, $contrato->fresh()->saldoPorCobrar());

        // Editar otra cosa del contrato no duplica el ingreso
        $contrato->update(['observacion' => 'editado']);
        $this->assertSame(1, Ingreso::where('numero_contrato', 'C-500')->where('modo_pago', 'CAN-CONTRATO')->count());
    }
}
