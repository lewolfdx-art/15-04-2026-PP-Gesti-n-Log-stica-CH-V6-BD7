<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class LogUserLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;
        $request = request();

        // Actualizar la fecha y IP del último login
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        // Registrar en logs
        Log::info("Usuario logueado: {$user->email} desde IP: {$request->ip()}");
    }
}