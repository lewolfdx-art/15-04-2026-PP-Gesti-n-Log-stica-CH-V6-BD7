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
            if ($agregar('page_title')) { $table->string('page_title')->nullable()->after('id'); }
            if ($agregar('page_description')) { $table->text('page_description')->nullable()->after('page_title'); }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['page_title', 'page_description']);
        });
    }
};