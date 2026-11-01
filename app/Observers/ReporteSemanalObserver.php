<?php

namespace App\Observers;

use App\Models\ReporteSemanal;
use App\Services\NotificationService;

class ReporteSemanalObserver
{
    public function created(ReporteSemanal $reporteSemanal): void
    {
        NotificationService::notifyReporteSemanal($reporteSemanal);
        NotificationService::checkAndNotifyPayables();
    }

    public function updated(ReporteSemanal $reporteSemanal): void
    {
        if ($reporteSemanal->wasChanged('estado')) {
            NotificationService::notifyReporteSemanal($reporteSemanal);
        }

        if ($reporteSemanal->wasChanged(['estado', 'monto'])) {
            NotificationService::checkAndNotifyPayables();
        }
    }

    public function deleted(ReporteSemanal $reporteSemanal): void
    {
        NotificationService::checkAndNotifyPayables();
    }
}
