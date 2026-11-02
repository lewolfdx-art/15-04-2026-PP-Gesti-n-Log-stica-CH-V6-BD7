<?php

namespace Tests\Feature;

use App\Models\Contrato;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Programación sin hora de término: el mismo vehículo a menos de 3 h el mismo día es un cruce probable;
 * más tarde ese día solo se informa.
 */
class ProgramacionCrucesTest extends TestCase
{
    use RefreshDatabase;

    private Vehiculo $mixer;

    private Vehiculo $bomba;

    private string $dia;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mixer = Vehiculo::create(['codigo' => 'MIXER-01', 'tipo' => 'mixer', 'activo' => true]);
        $this->bomba = Vehiculo::create(['codigo' => 'BOMBA-01', 'tipo' => 'bomba_estacionaria', 'activo' => true]);
        $this->dia = today()->addDays(2)->toDateString();

        $programado = $this->contrato(['fecha' => $this->dia, 'hora_inicio' => '08:00', 'programado_at' => now()]);
        $programado->vehiculos()->attach($this->mixer->id);
    }

    public function test_el_mismo_vehiculo_dos_horas_despues_es_cruce_probable(): void
    {
        $conflictos = Contrato::conflictosDeVehiculos(null, $this->dia, '10:00', [$this->mixer->id]);

        $this->assertCount(1, $conflictos);
        $this->assertTrue($conflictos->first()->esCruce);
    }

    public function test_el_mismo_vehiculo_seis_horas_despues_solo_se_informa(): void
    {
        $conflictos = Contrato::conflictosDeVehiculos(null, $this->dia, '14:00', [$this->mixer->id]);

        $this->assertCount(1, $conflictos);
        $this->assertFalse($conflictos->first()->esCruce);
    }

    public function test_otro_vehiculo_u_otro_dia_no_tiene_conflictos(): void
    {
        $this->assertCount(0, Contrato::conflictosDeVehiculos(null, $this->dia, '08:00', [$this->bomba->id]));
        $this->assertCount(0, Contrato::conflictosDeVehiculos(null, today()->addDays(9)->toDateString(), '08:00', [$this->mixer->id]));
    }

    public function test_el_horario_se_muestra_solo_con_la_hora_de_inicio(): void
    {
        $contrato = $this->contrato(['hora_inicio' => '07:30:00', 'hora_fin' => '11:00:00']);

        $this->assertSame('07:30', $contrato->horarioTexto());
    }
}
