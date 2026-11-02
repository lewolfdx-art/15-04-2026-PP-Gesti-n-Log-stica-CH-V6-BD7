<?php

namespace Tests\Feature;

use App\Filament\Widgets\AgendaVaciadosWidget;
use App\Filament\Widgets\FechasSeparadasWidget;
use App\Filament\Widgets\ResumenHoyWidget;
use App\Models\Cotizacion;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Cada widget del Escritorio se dibuja sin errores. Los widgets cargan aparte de la página,
 * así que un error en uno (como un estado de cotización sin color) no aparece al abrir /admin.
 */
class EscritorioWidgetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_todos_los_widgets_del_escritorio_cargan(): void
    {
        $this->actingAs($this->usuarioConRol('super_admin'));
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        // Una cotización en cada estado, para que los gráficos tengan todos los casos
        $asesor = $this->asesor();
        foreach (array_keys(Cotizacion::ESTADOS) as $estado) {
            Cotizacion::create(['asesor_id' => $asesor->id, 'cliente_nombre' => "Cliente {$estado}", 'fecha_tentativa' => today()->addDay(), 'hora_tentativa' => '08:00'])
                ->forceFill(['estado' => $estado, 'adelanto_monto' => 100])->save();
        }
        $this->contrato(['fecha' => today(), 'hora_inicio' => '09:00', 'programado_at' => now()]);

        $widgets = collect(glob(app_path('Filament/Widgets/*.php')))
            ->map(fn (string $archivo) => 'App\\Filament\\Widgets\\' . basename($archivo, '.php'));

        $this->assertGreaterThanOrEqual(12, $widgets->count());

        foreach ($widgets as $widget) {
            Livewire::test($widget)->assertOk();
        }
    }

    public function test_la_agenda_muestra_los_proximos_vaciados_aunque_falten_semanas(): void
    {
        $this->actingAs($this->usuarioConRol('super_admin'));
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $semana = $this->contrato(['fecha' => today()->addDays(3), 'nombre_cliente' => 'Cliente esta semana']);
        $finDeMes = $this->contrato(['fecha' => today()->addDays(17), 'nombre_cliente' => 'Cliente fin de mes']);
        $pasado = $this->contrato(['fecha' => today()->subDay(), 'nombre_cliente' => 'Cliente de ayer']);

        Livewire::test(AgendaVaciadosWidget::class)
            ->assertCanSeeTableRecords([$semana, $finDeMes], inOrder: true)
            ->assertCanNotSeeTableRecords([$pasado])
            ->assertSee('1 vaciado en los próximos 7 días');

        $semana->delete();
        Livewire::test(AgendaVaciadosWidget::class)
            ->assertCanSeeTableRecords([$finDeMes])
            ->assertSee('Esta semana no hay vaciados');
    }

    public function test_el_resumen_de_hoy_indica_el_proximo_vaciado(): void
    {
        $this->actingAs($this->usuarioConRol('super_admin'));
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->contrato(['fecha' => today()->addDays(17), 'nombre_cliente' => 'Rodolfo'])
            ->forceFill(['programado_at' => now()])->save();

        Livewire::test(ResumenHoyWidget::class)
            ->assertSee('Próximo:')
            ->assertSee('Rodolfo')
            ->assertSee('El próximo ya está programado');
    }

    public function test_fechas_separadas_muestra_solo_las_que_aun_no_son_contrato(): void
    {
        $this->actingAs($this->usuarioConRol('super_admin'));
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $asesor = $this->asesor();
        $nueva = fn (string $cliente) => Cotizacion::create(['asesor_id' => $asesor->id, 'cliente_nombre' => $cliente, 'volumen_estimado' => 20, 'precio_unitario_propuesto' => 300, 'fecha_tentativa' => today()->addDays(10), 'hora_tentativa' => '08:00']);

        $this->assertFalse(FechasSeparadasWidget::canView(), 'Sin fechas separadas no se muestra');

        $separada = $nueva('Separó fecha');
        $separada->registrarAdelanto(300, 'YAPE_PLIN');
        $sinAdelanto = $nueva('Solo cotizó');
        $convertida = $nueva('Ya es contrato');
        $convertida->forceFill(['estado' => 'convertida', 'adelanto_monto' => 300])->save();

        $this->assertTrue(FechasSeparadasWidget::canView());
        Livewire::test(FechasSeparadasWidget::class)
            ->assertCanSeeTableRecords([$separada])
            ->assertCanNotSeeTableRecords([$sinAdelanto, $convertida]);
    }
}
