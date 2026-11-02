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

        DB::table('contratos as c')
            ->join('trabajadores as t', function ($join) {
                $join->on('t.nombre_completo', '=', 'c.nombre_vendedor')->where('t.tipo_cargo', 'asesor_ventas');
            })
            ->whereNull('c.vendedor_id')
            ->update(['c.vendedor_id' => DB::raw('t.id')]);
    }

    public function down(): void
    {
        // No se revierte: DEBE y PENDIENTE significan lo mismo y el id del vendedor es correcto
    }
};
