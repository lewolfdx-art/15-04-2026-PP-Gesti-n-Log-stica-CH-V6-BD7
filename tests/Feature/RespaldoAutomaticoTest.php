<?php

namespace Tests\Feature;

use App\Support\RespaldoAutomatico;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Respaldo cada 14 días sin programador de tareas. Usa una carpeta de storage temporal:
 * nunca toca storage/app/backups real ni ejecuta mysqldump.
 */
class RespaldoAutomaticoTest extends TestCase
{
    private string $storageTemporal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storageTemporal = sys_get_temp_dir() . '/concretera-pruebas-' . uniqid();
        File::ensureDirectoryExists($this->storageTemporal . '/app/backups');
        $this->app->useStoragePath($this->storageTemporal);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->storageTemporal);
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function respaldoDeHace(int $dias): void
    {
        $archivo = storage_path('app/backups/backup_MANUAL_prueba_' . $dias . '.zip');
        file_put_contents($archivo, 'x');
        touch($archivo, now()->subDays($dias)->getTimestamp());
    }

    public function test_sin_respaldos_toca_hacer_uno(): void
    {
        $this->assertNull(RespaldoAutomatico::ultimo());
        $this->assertTrue(RespaldoAutomatico::toca());
    }

    public function test_con_un_respaldo_reciente_no_toca(): void
    {
        $this->respaldoDeHace(3);

        $this->assertFalse(RespaldoAutomatico::toca());
        $this->assertSame(now()->subDays(3)->addDays(14)->toDateString(), RespaldoAutomatico::proximo()->toDateString());
    }

    public function test_a_los_catorce_dias_toca_otra_vez(): void
    {
        $this->respaldoDeHace(15);

        $this->assertTrue(RespaldoAutomatico::toca());
    }

    public function test_la_fecha_del_ultimo_respaldo_esta_en_hora_de_lima(): void
    {
        $this->respaldoDeHace(1);

        $this->assertSame('America/Lima', RespaldoAutomatico::ultimo()->getTimezone()->getName());
    }
}
