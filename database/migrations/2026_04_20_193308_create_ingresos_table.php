<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            
            $table->date('fecha');
            $table->string('modo_pago');           // CAN-CONTRATO, ADEL-CONTRATO, IN-OTROS
            $table->string('numero_contrato')->nullable();
            $table->string('asesor')->nullable();
            $table->text('detalle')->nullable();
            $table->decimal('monto', 12, 2);
            $table->string('banco')->nullable();   // EFECTIVO, CTA.CTE
            $table->string('obs')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ingresos');
    }
};