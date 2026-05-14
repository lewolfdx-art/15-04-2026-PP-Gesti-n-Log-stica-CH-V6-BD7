<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MakeSuperAdmin extends Command
{
    protected $signature = 'make:superadmin';
    protected $description = 'Crea el Super Admin usando credenciales del .env';

    public function handle()
    {
        $name     = 'Super Admin';
        $email    = env('SUPER_ADMIN_EMAIL', 'geremy_rko56@hotmail.com');
        $password = env('SUPER_ADMIN_PASSWORD', 'NOMERCY1881mm');

        if (empty($password)) {
            $this->error('❌ No se encontró SUPER_ADMIN_PASSWORD en el .env');
            return 1;
        }

        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'admin']);

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'       => $name,
                'password'   => Hash::make($password),
                'is_active'  => true,
            ]
        );

        $user->syncRoles(['super_admin']);

        $this->info("✅ SUPER ADMIN CONFIGURADO CORRECTAMENTE");
        $this->info("Email: " . $email);
        $this->newLine();
        $this->warn("→ Listo para usar en esta PC");
    }
}