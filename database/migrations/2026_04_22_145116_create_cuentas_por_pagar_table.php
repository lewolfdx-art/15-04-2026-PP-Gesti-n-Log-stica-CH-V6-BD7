<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cuentas_por_pagar', function (Blueprint $table) {
            $table->id();
            $table->string('detalle');
            $table->string('tipo'); // fecha, mes, fin_mes, viajes
            $table->date('fecha')->nullable();
            $table->string('mes')->nullable();
            $table->string('año')->nullable();
            $table->string('cantidad')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->boolean('pagado')->default(false); // TOTALP
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_por_pagar');
    }
};
