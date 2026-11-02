<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Solo las columnas que falten: migrar una base nueva fallaba con \"columna duplicada\"
            $agregar = fn (string $columna) => ! Schema::hasColumn('productos', $columna);
            if ($agregar('tipos_concreto')) { $table->text('tipos_concreto')->nullable()->after('page_description'); }
            if ($agregar('servicios_complementarios')) { $table->text('servicios_complementarios')->nullable()->after('tipos_concreto'); }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['tipos_concreto', 'servicios_complementarios']);
        });
    }
};