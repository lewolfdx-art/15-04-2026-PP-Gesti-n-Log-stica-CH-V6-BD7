<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Renombrar la columna de anio a año
        Schema::table('reportes_semanales', function (Blueprint $table) {
            $table->renameColumn('anio', 'año');
        });
    }

    public function down(): void
    {
        Schema::table('reportes_semanales', function (Blueprint $table) {
            $table->renameColumn('año', 'anio');
        });
    }
};