<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cuentas_por_pagar', function (Blueprint $table) {
            $table->string('cantidad')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cuentas_por_pagar', function (Blueprint $table) {
            $table->integer('cantidad')->nullable()->change();
        });
    }
};