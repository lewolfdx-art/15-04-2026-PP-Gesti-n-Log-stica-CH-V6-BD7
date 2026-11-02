<?php

use App\Models\Contrato;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Los contratos que ya estaban PAGADO no tenían su ingreso, así que el Balance no reflejaba
     * las ventas. Se registra el saldo de cada uno con la fecha del vaciado (no se sabe la del pago).
     * Desde ahora lo hace solo el modelo Contrato al marcarse PAGADO.
     */
    public function up(): void
    {
        Contrato::where('estado_pago', 'PAGADO')->get()
            ->each(fn (Contrato $contrato) => $contrato->registrarCancelacion($contrato->fecha?->toDateString()));
    }

    public function down(): void
    {
        // Los ingresos quedan: borrarlos descuadraría la caja si ya se usaron en reportes
    }
};
