<?php

namespace Database\Seeders;

use App\Models\DatoOperacion;
use Illuminate\Database\Seeder;

class DatoOperacionSeeder extends Seeder
{
    public function run()
    {
        // ==================== BORRAR SOLO LOS DATOS GENERALES ====================
        DatoOperacion::whereIn('tipo', [
            'modo_pago', 
            'banco', 
            'cancelacion', 
            'estado'
        ])->delete();

        // ==================== INSERTAR / ACTUALIZAR DATOS GENERALES ====================
        $datosGenerales = [
            // Modo de Pago
            ['tipo' => 'modo_pago', 'valor' => 'EFECTIVO', 'orden' => 1],
            ['tipo' => 'modo_pago', 'valor' => 'EFECTIVO DEPOSITO', 'orden' => 2],
            ['tipo' => 'modo_pago', 'valor' => 'ADEL CONTRATO', 'orden' => 3],
            ['tipo' => 'modo_pago', 'valor' => 'PRESTAMO', 'orden' => 4],
            ['tipo' => 'modo_pago', 'valor' => 'CAN-CONTRATO', 'orden' => 5],
            ['tipo' => 'modo_pago', 'valor' => 'DEPOSITO-EFECTIVO', 'orden' => 6],
            ['tipo' => 'modo_pago', 'valor' => 'VENTA DE SOBRANTE', 'orden' => 7],
            ['tipo' => 'modo_pago', 'valor' => 'ALQUILER', 'orden' => 8],
            ['tipo' => 'modo_pago', 'valor' => 'PAGO DE PERSONAL', 'orden' => 9],
            ['tipo' => 'modo_pago', 'valor' => 'IN-OTROS', 'orden' => 10],

            // Banco
            ['tipo' => 'banco', 'valor' => 'BCP', 'orden' => 1],
            ['tipo' => 'banco', 'valor' => 'BBVA', 'orden' => 2],

            // Cancelación
            ['tipo' => 'cancelacion', 'valor' => 'PAGADO', 'orden' => 1],
            ['tipo' => 'cancelacion', 'valor' => 'PERDIDA', 'orden' => 2],
            ['tipo' => 'cancelacion', 'valor' => 'DEBE', 'orden' => 3],

            // Estado
            ['tipo' => 'estado', 'valor' => 'ACTIVO', 'orden' => 1],
            ['tipo' => 'estado', 'valor' => 'INACTIVO', 'orden' => 2],
        ];

        foreach ($datosGenerales as $dato) {
            DatoOperacion::updateOrCreate(
                ['tipo' => $dato['tipo'], 'valor' => $dato['valor']],  // busca por tipo + valor
                [
                    'descripcion' => null,
                    'orden'       => $dato['orden'],
                    'activo'      => true,
                    //'es_persona'  => false,
                ]
            );
        }

        $this->command->info('✅ Datos generales actualizados correctamente (' . count($datosGenerales) . ' registros).');
        $this->command->info('Los trabajadores y asesores NO fueron borrados.');
    }
}