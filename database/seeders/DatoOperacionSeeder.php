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
            ['tipo' => 'asesor', 'valor' => 'ANA HUAMAN', 'orden' => 1],
            ['tipo' => 'asesor', 'valor' => 'GINA PILAR', 'orden' => 2],
            ['tipo' => 'asesor', 'valor' => 'NICOLAS ASESOR', 'orden' => 3],
            ['tipo' => 'asesor', 'valor' => 'MIGUEL ALBERTO', 'orden' => 4],
            ['tipo' => 'asesor', 'valor' => 'JUAN VELIZ', 'orden' => 5],
            ['tipo' => 'asesor', 'valor' => 'FRANK LIDER MIX', 'orden' => 6],
            ['tipo' => 'asesor', 'valor' => 'RODRIGO VEGA', 'orden' => 7],
            ['tipo' => 'asesor', 'valor' => 'PAGEL ORIHUELA', 'orden' => 8],
            ['tipo' => 'asesor', 'valor' => 'NICK', 'orden' => 9],
            ['tipo' => 'asesor', 'valor' => 'FRANZ ALBERTO', 'orden' => 10],
            ['tipo' => 'asesor', 'valor' => 'DANI GUILLERMO', 'orden' => 11],
            ['tipo' => 'asesor', 'valor' => 'YEMI CEMIXCON', 'orden' => 12],
            ['tipo' => 'asesor', 'valor' => 'FSTEFANY CEMIXCON', 'orden' => 13],

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