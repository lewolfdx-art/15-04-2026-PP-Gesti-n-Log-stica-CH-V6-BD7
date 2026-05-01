<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->text('tipos_concreto')->nullable()->after('page_description');
            $table->text('servicios_complementarios')->nullable()->after('tipos_concreto');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['tipos_concreto', 'servicios_complementarios']);
        });
    }
};