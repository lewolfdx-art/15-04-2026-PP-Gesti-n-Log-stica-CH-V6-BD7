<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * - estado_pago: el formulario solo maneja PAGADO / DEBE; los "PENDIENTE" antiguos pasan a DEBE.
     * - vendedor_id: se llena con el id del asesor según el nombre, para que las comisiones
     *   no dependan del texto (corregir una tilde en el nombre ya no las pierde).
     */
    public function up(): void
    {
        DB::table('contratos')->where('estado_pago', 'PENDIENTE')->update(['estado_pago' => 'DEBE']);

        // Subconsulta en vez de UPDATE ... JOIN: así también funciona en SQLite (pruebas, instalación nueva)
        DB::table('contratos')
            ->whereNull('vendedor_id')
            ->update(['vendedor_id' => DB::raw(
                "(SELECT t.id FROM trabajadores t WHERE t.nombre_completo = contratos.nombre_vendedor AND t.tipo_cargo = 'asesor_ventas' LIMIT 1)"
            )]);
    }

    public function down(): void
    {
        // No se revierte: DEBE y PENDIENTE significan lo mismo y el id del vendedor es correcto
    }
};
