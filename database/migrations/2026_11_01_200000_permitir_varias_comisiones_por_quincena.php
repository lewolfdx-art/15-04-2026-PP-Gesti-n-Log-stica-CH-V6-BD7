<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Las comisiones se pueden liquidar por obra, así que un asesor puede tener varios pagos
     * en la misma quincena. Lo que evita pagar dos veces es la marca pago_comision_id del contrato;
     * la regla de un sueldo por quincena queda en PagoPersonal::registrar().
     */
    public function up(): void
    {
        Schema::table('pagos_personal', function (Blueprint $table) {
            // Primero el índice normal: la llave foránea de trabajador_id se apoya en el único
            $table->index(['trabajador_id', 'tipo', 'anio', 'mes', 'quincena'], 'pagos_personal_quincena_index');
            $table->dropUnique('pagos_personal_quincena_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pagos_personal', function (Blueprint $table) {
            $table->unique(['trabajador_id', 'tipo', 'anio', 'mes', 'quincena'], 'pagos_personal_quincena_unique');
            $table->dropIndex('pagos_personal_quincena_index');
        });
    }
};
