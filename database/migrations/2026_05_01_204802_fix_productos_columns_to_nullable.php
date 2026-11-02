<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Columnas antiguas que ya no se usan: quedan opcionales. create_productos_table se reescribió sin ellas,
        // así que en una base nueva no existen: se crean opcionales para que el esquema sea el mismo que en producción.
        $columnas = [
            'name'            => fn (Blueprint $t) => $t->string('name')->nullable(),
            'slug'            => fn (Blueprint $t) => $t->string('slug')->nullable(),
            'description'     => fn (Blueprint $t) => $t->text('description')->nullable(),
            'price'           => fn (Blueprint $t) => $t->decimal('price', 10, 2)->nullable(),
            'stock'           => fn (Blueprint $t) => $t->integer('stock')->nullable(),
            'category'        => fn (Blueprint $t) => $t->string('category')->nullable(),
            'type'            => fn (Blueprint $t) => $t->string('type')->nullable(),
            'technical_specs' => fn (Blueprint $t) => $t->text('technical_specs')->nullable(),
        ];

        Schema::table('productos', function (Blueprint $table) use ($columnas) {
            foreach ($columnas as $nombre => $definir) {
                $columna = $definir($table);
                if (Schema::hasColumn('productos', $nombre)) {
                    $columna->change();
                }
            }
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