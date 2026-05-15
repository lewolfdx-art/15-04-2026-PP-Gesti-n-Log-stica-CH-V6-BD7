<?php
// app/Observers/ReporteSemanalObserver.php

namespace App\Observers;

use App\Models\ReporteSemanal;
use App\Services\NotificationService;

class ReporteSemanalObserver
{
    public function created(ReporteSemanal $reporteSemanal)
    {
        NotificationService::notifyReporteSemanal($reporteSemanal);
        NotificationService::checkAndNotifyPayables();
    }
    
    public function updated(ReporteSemanal $reporteSemanal)
    {
        // Si cambió el estado
        if ($reporteSemanal->wasChanged('estado')) {
            NotificationService::notifyReporteSemanal($reporteSemanal);
        }
        
        NotificationService::checkAndNotifyPayables();
    }
    
    public function deleted(ReporteSemanal $reporteSemanal)
    {
        NotificationService::checkAndNotifyPayables();
    }
}