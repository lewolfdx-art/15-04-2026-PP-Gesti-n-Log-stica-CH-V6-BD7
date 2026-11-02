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
        $email    = env('SUPER_ADMIN_EMAIL');
        // Sin valor por defecto: la contraseña nunca debe estar escrita en el código
        $password = env('SUPER_ADMIN_PASSWORD');

        if (empty($email) || empty($password)) {
            $this->error('Define SUPER_ADMIN_EMAIL y SUPER_ADMIN_PASSWORD en el .env');
            return 1;
        }

        if (strlen($password) < 12) {
            $this->error('SUPER_ADMIN_PASSWORD debe tener al menos 12 caracteres');
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

        $this->info("SUPER ADMIN CONFIGURADO CORRECTAMENTE");
        $this->info("Email: " . $email);
        $this->newLine();
        $this->warn("→ Listo para usar en esta PC");
    }
}