<?php
// app/Observers/CuentaPorPagarObserver.php

namespace App\Observers;

use App\Models\CuentaPorPagar;
use App\Services\NotificationService;

class CuentaPorPagarObserver
{
    public function created(CuentaPorPagar $cuentaPorPagar)
    {
        // Notificar nueva cuenta por pagar
        NotificationService::notifyNewPayable($cuentaPorPagar);
        
        // Verificar total general
        NotificationService::checkAndNotifyPayables();
    }
    
    public function updated(CuentaPorPagar $cuentaPorPagar)
    {
        // Si cambió el estado 'pagado' de false a true
        if ($cuentaPorPagar->wasChanged('pagado') && $cuentaPorPagar->pagado == true) {
            NotificationService::notifyPayablePaid($cuentaPorPagar);
        }
        
        // Verificar total general después del cambio
        NotificationService::checkAndNotifyPayables();
    }
    
    public function deleted(CuentaPorPagar $cuentaPorPagar)
    {
        NotificationService::checkAndNotifyPayables();
    }
}