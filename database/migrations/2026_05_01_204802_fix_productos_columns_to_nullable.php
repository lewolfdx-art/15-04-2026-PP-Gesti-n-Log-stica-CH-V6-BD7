<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Hacer NULL las columnas antiguas que ya no usamos
            $table->string('name')->nullable()->change();
            $table->string('slug')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->decimal('price', 10, 2)->nullable()->change();
            $table->integer('stock')->nullable()->change();
            $table->string('category')->nullable()->change();
            $table->string('type')->nullable()->change();
            $table->text('technical_specs')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('slug')->nullable(false)->change();
        });
    }
};