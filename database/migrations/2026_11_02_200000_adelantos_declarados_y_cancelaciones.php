<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * - El asesor declara el adelanto que recibió en la obra (monto, forma de pago, foto del voucher);
     *   la administración lo confirma y en ese momento se separa la fecha.
     * - Si el cliente cancela una cotización con adelanto, se registra la devolución.
     */
    public function up(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->decimal('adelanto_declarado_monto', 12, 2)->nullable()->after('adelanto_forma_pago');
            $table->string('adelanto_declarado_forma_pago', 30)->nullable()->after('adelanto_declarado_monto');
            $table->string('adelanto_voucher')->nullable()->after('adelanto_declarado_forma_pago');
            $table->timestamp('adelanto_declarado_at')->nullable()->after('adelanto_voucher');

            $table->text('motivo_cancelacion')->nullable()->after('motivo_rechazo');
            $table->string('devolucion_forma_pago', 30)->nullable()->after('motivo_cancelacion');
            $table->timestamp('devolucion_at')->nullable()->after('devolucion_forma_pago');
        });
    }

    public function down(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->dropColumn([
                'adelanto_declarado_monto', 'adelanto_declarado_forma_pago', 'adelanto_voucher', 'adelanto_declarado_at',
                'motivo_cancelacion', 'devolucion_forma_pago', 'devolucion_at',
            ]);
        });
    }
};
