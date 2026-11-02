<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Mixers y bombas: se asignan a cada vaciado y no pueden estar en dos obras a la vez
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();        // Ej: MIXER-01
            $table->string('tipo', 30);                     // mixer | bomba_telescopica | bomba_estacionaria | otro
            $table->string('placa', 15)->nullable();
            $table->decimal('capacidad_m3', 6, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });

        // Cotización que el asesor registra en la obra; el admin la aprueba y la pasa a contrato
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();                                   // COT-2026-00001
            $table->foreignId('asesor_id')->constrained('trabajadores')->restrictOnDelete();
            $table->string('estado', 20)->default('enviada');                         // enviada | en_espera | convertida | rechazada

            $table->string('cliente_nombre');
            $table->string('cliente_documento', 20)->nullable();
            $table->string('cliente_telefono', 30)->nullable();

            $table->text('ubicacion_referencia')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->text('descripcion_lugar')->nullable();
            $table->json('fotos_lugar')->nullable();
            $table->string('viabilidad', 20)->nullable();
            $table->text('viabilidad_comentario')->nullable();

            $table->string('estructura')->nullable();
            $table->string('tipo_concreto', 10)->nullable();
            $table->decimal('volumen_estimado', 8, 2)->nullable();
            $table->string('bombeo', 20)->nullable();
            $table->date('fecha_tentativa')->nullable();
            $table->time('hora_tentativa')->nullable();

            $table->decimal('precio_unitario_propuesto', 10, 2)->nullable();
            $table->decimal('precio_unitario_aprobado', 10, 2)->nullable();

            $table->decimal('adelanto_monto', 12, 2)->nullable();
            $table->timestamp('adelanto_at')->nullable();
            $table->string('adelanto_forma_pago', 30)->nullable();

            $table->text('observaciones')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->timestamp('enviada_at')->nullable();
            $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('revisado_at')->nullable();
            $table->timestamps();
        });

        // Contrato: de qué cotización viene y su programación (hora y vehículos)
        Schema::table('contratos', function (Blueprint $table) {
            $table->foreignId('cotizacion_id')->nullable()->after('id')->constrained('cotizaciones')->nullOnDelete();
            $table->time('hora_inicio')->nullable()->after('fecha');
            $table->time('hora_fin')->nullable()->after('hora_inicio');
            $table->timestamp('programado_at')->nullable()->after('hora_fin');
            $table->foreignId('programado_por')->nullable()->after('programado_at')->constrained('users')->nullOnDelete();
        });

        Schema::create('contrato_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnDelete();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['contrato_id', 'vehiculo_id']);
        });

        // Rol del asesor de ventas para registrar cotizaciones desde el celular
        Role::firstOrCreate(['name' => 'asesor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'coordinador_de_vaciados', 'guard_name' => 'web']);
    }

    public function down(): void
    {
        Schema::dropIfExists('contrato_vehiculo');
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('programado_por');
            $table->dropConstrainedForeignId('cotizacion_id');
            $table->dropColumn(['hora_inicio', 'hora_fin', 'programado_at']);
        });
        Schema::dropIfExists('cotizaciones');
        Schema::dropIfExists('vehiculos');
        Role::where('name', 'asesor')->where('guard_name', 'web')->delete();
    }
};
