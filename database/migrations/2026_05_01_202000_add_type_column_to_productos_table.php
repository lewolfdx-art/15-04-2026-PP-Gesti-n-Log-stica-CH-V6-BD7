<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sin after('category'): en una base nueva esa columna aún no existe y MySQL falla
        if (! Schema::hasColumn('productos', 'type')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->string('type')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};