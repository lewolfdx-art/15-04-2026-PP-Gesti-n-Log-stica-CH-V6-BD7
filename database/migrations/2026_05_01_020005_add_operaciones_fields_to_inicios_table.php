<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inicios', function (Blueprint $table) {
            $table->json('nuestras_operaciones')->nullable()->after('proyectos_destacados');
            $table->string('whatsapp_number')->nullable()->after('social_networks');
        });
    }

    public function down(): void
    {
        Schema::table('inicios', function (Blueprint $table) {
            $table->dropColumn(['nuestras_operaciones', 'whatsapp_number']);
        });
    }
};