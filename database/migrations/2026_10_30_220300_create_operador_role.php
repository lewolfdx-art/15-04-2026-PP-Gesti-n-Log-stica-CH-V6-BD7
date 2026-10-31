<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Rol para los trabajadores que entran al panel /operador a ver sus obras
        Role::firstOrCreate(['name' => 'operador', 'guard_name' => 'web']);
    }

    public function down(): void
    {
        Role::where('name', 'operador')->where('guard_name', 'web')->delete();
    }
};
