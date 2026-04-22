<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cuentas_por_pagar', function (Blueprint $table) {
            $table->string('año')->nullable()->after('mes');
        });
    }
    
    public function down(): void
    {
        Schema::table('cuentas_por_pagar', function (Blueprint $table) {
            $table->dropColumn('año');
        });
    }
};
