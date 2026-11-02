<?php

namespace Tests\Feature;

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
}
