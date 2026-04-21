<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            // Solo agregamos 'grupo' (subcategoria ya existe)
            if (!Schema::hasColumn('gastos', 'grupo')) {
                $table->string('grupo')->nullable()->after('fecha');
            }

            // Hacemos nullable el gasto_categoria_id para evitar conflictos
            if (Schema::hasColumn('gastos', 'gasto_categoria_id')) {
                $table->foreignId('gasto_categoria_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('gastos', function (Blueprint $table) {
            if (Schema::hasColumn('gastos', 'grupo')) {
                $table->dropColumn('grupo');
            }
        });
    }
};