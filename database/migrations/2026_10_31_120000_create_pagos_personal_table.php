<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sueldo fijo del personal (operadores, planta, administración)
        Schema::table('trabajadores', function (Blueprint $table) {
            $table->decimal('sueldo_mensual', 10, 2)->nullable()->after('tipo_cargo');
        });

        // Pagos quincenales: sueldo para el personal, comisión para los asesores de ventas
        Schema::create('pagos_personal', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();                  // PP-2026-00001
            $table->foreignId('trabajador_id')->constrained('trabajadores')->restrictOnDelete();
            $table->string('tipo', 10);                               // sueldo | comision
            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('mes');
            $table->unsignedTinyInteger('quincena');                  // 1: del 1 al 15, 2: del 16 a fin de mes
            $table->date('periodo_desde');
            $table->date('periodo_hasta');
            $table->decimal('monto_base', 12, 2);
            $table->decimal('adicionales', 12, 2)->default(0);
            $table->string('concepto_adicionales')->nullable();
            $table->decimal('descuentos', 12, 2)->default(0);
            $table->string('concepto_descuentos')->nullable();
            $table->decimal('total', 12, 2);
            $table->string('forma_pago', 30)->nullable();
            $table->text('observacion')->nullable();
            $table->timestamp('pagado_at')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Una sola vez por trabajador, tipo y quincena
            $table->unique(['trabajador_id', 'tipo', 'anio', 'mes', 'quincena'], 'pagos_personal_quincena_unique');
        });

        // Marca en el contrato con qué pago se liquidó la comisión del asesor
        Schema::table('contratos', function (Blueprint $table) {
            $table->foreignId('pago_comision_id')->nullable()->after('comision_igv')->constrained('pagos_personal')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pago_comision_id');
        });
        Schema::dropIfExists('pagos_personal');
        Schema::table('trabajadores', function (Blueprint $table) {
            $table->dropColumn('sueldo_mensual');
        });
    }
};
