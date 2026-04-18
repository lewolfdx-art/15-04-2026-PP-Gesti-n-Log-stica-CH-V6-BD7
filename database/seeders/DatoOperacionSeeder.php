<?php

namespace Database\Seeders;

use App\Models\DatoOperacion;
use Illuminate\Database\Seeder;

class DatoOperacionSeeder extends Seeder
{
    public function run()
    {
        DatoOperacion::truncate(); // Borra datos anteriores

        $datos = [
            // ==================== MODO PAGO ====================
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

            // ==================== BANCO ====================
            ['tipo' => 'banco', 'valor' => 'BCP', 'orden' => 1],
            ['tipo' => 'banco', 'valor' => 'BBVA', 'orden' => 2],

            // ==================== ASESOR ====================


            // ==================== CANCELACION ====================
            ['tipo' => 'cancelacion', 'valor' => 'PAGADO', 'orden' => 1],
            ['tipo' => 'cancelacion', 'valor' => 'PERDIDA', 'orden' => 2],
            ['tipo' => 'cancelacion', 'valor' => 'DEBE', 'orden' => 3],

            // ==================== OTROS (puedes agregar más tipos aquí) ====================
            ['tipo' => 'estado', 'valor' => 'ACTIVO', 'orden' => 1],
            ['tipo' => 'estado', 'valor' => 'INACTIVO', 'orden' => 2],
        ];

        foreach ($datos as $dato) {
            DatoOperacion::create([
                'tipo'        => $dato['tipo'],
                'valor'       => $dato['valor'],
                'descripcion' => null,
                'orden'       => $dato['orden'],
                'activo'      => true,
            ]);
        }

        $this->command->info('✅ DatoOperacionSeeder ejecutado correctamente con ' . count($datos) . ' registros.');
    }
}