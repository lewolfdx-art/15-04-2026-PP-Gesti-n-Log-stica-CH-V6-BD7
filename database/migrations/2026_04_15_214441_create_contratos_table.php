<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            
            $table->date('fecha')->default(now()->toDateString())->index();
            
            $table->string('estructura')->nullable();           // ← cambiado a nullable
            $table->text('nombre_cliente')->nullable();
            $table->string('nombre_vendedor')->nullable();      // ← nullable por ahora
            $table->string('numero_contrato')->nullable();
            $table->string('tipo_concreto')->nullable();
            
            $table->string('guia_remision')->nullable();
            $table->string('factura')->nullable();
            
            $table->string('bombeo')->nullable();
            $table->decimal('bomba_adicional', 12, 2)->default(0);
            
            $table->decimal('volumen_real', 10, 2)->default(0);
            $table->decimal('volumen_guia', 10, 2)->default(0);
            $table->decimal('volumen_sobrante', 10, 2)->default(0);
            
            $table->decimal('pu', 12, 2)->nullable();                    // ← nullable
            $table->decimal('monto_total', 14, 2)->nullable();           // ← nullable
            $table->decimal('descuento_guias', 12, 2)->default(0);
            $table->decimal('venta_neta', 14, 2)->nullable();            // ← nullable
            $table->decimal('pu_real', 12, 2)->nullable();
            
            $table->string('estado_pago')->default('PAGADO');
            $table->string('forma_pago')->nullable();
            $table->string('cancelacion')->nullable();
            $table->text('observacion')->nullable();
            $table->text('observaciones_adicionales')->nullable();
            
            $table->decimal('comision', 12, 2)->default(0);
            $table->decimal('comision_igv', 12, 2)->default(0);
            $table->decimal('total_para_empresa', 14, 2)->nullable();
            
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('vendedor_id')->nullable();
            $table->unsignedBigInteger('gasto_categoria_id')->nullable();
            
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['fecha', 'nombre_vendedor']);
            $table->index('numero_contrato');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
