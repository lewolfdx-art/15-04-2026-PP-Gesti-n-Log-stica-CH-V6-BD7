<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos que exige la Hoja de Reclamación (Ley 29571 y su reglamento).
     */
    public function up(): void
    {
        Schema::table('libro_reclamacions', function (Blueprint $table) {
            $table->string('codigo', 20)->nullable()->unique()->after('id'); // LR-2026-00001
            $table->boolean('menor_edad')->default(false)->after('address');
            $table->string('apoderado_nombre')->nullable()->after('menor_edad');
            $table->string('bien_tipo', 10)->nullable()->after('order_number'); // producto | servicio
            $table->text('bien_descripcion')->nullable()->after('bien_tipo');
            $table->decimal('monto', 12, 2)->nullable()->after('bien_descripcion');
            $table->string('tipo', 10)->default('reclamo')->after('monto'); // reclamo | queja
            $table->date('fecha_limite')->nullable()->after('status');
            $table->timestamp('respondido_at')->nullable()->after('response');
            $table->foreignId('respondido_por')->nullable()->after('respondido_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('libro_reclamacions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('respondido_por');
            $table->dropColumn(['codigo', 'menor_edad', 'apoderado_nombre', 'bien_tipo', 'bien_descripcion', 'monto', 'tipo', 'fecha_limite', 'respondido_at']);
        });
    }
};
