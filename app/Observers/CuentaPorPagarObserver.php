<?php

namespace App\Observers;

use App\Models\CuentaPorPagar;
use App\Services\NotificationService;

class CuentaPorPagarObserver
{
    public function created(CuentaPorPagar $cuentaPorPagar): void
    {
        if (! $cuentaPorPagar->pagado) {
            NotificationService::notifyNewPayable($cuentaPorPagar);
        }

        NotificationService::checkAndNotifyPayables();
    }

    public function updated(CuentaPorPagar $cuentaPorPagar): void
    {
        // Solo cuando pasa de pendiente a pagada
        if ($cuentaPorPagar->wasChanged('pagado') && $cuentaPorPagar->pagado) {
            NotificationService::notifyPayablePaid($cuentaPorPagar);
        }

        if ($cuentaPorPagar->wasChanged(['pagado', 'total'])) {
            NotificationService::checkAndNotifyPayables();
        }
    }

    public function deleted(CuentaPorPagar $cuentaPorPagar): void
    {
        NotificationService::checkAndNotifyPayables();
    }
}
