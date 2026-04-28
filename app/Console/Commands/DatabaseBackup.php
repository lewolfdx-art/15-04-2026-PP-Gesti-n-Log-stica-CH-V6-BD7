<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use ZipArchive;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--monthly}';
    protected $description = 'Crea backup de la base de datos MySQL (diario o mensual) con compresión';

    public function handle()
    {
        $isMonthly = $this->option('monthly');

        $dbHost = Config::get('database.connections.mysql.host');
        $dbPort = Config::get('database.connections.mysql.port') ?? '3306';
        $dbName = Config::get('database.connections.mysql.database');
        $dbUser = Config::get('database.connections.mysql.username');
        $dbPass = Config::get('database.connections.mysql.password');

        $date = Carbon::now()->format('Y-m-d_H-i-s');
        $type = $isMonthly ? 'MENSUAL' : 'DIARIO';

        $backupPath = storage_path('app/backups');
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $sqlFilename = "backup_{$type}_{$dbName}_{$date}.sql";
        $zipFilename = "backup_{$type}_{$dbName}_{$date}.zip";
        
        $sqlPath = "{$backupPath}/{$sqlFilename}";
        $zipPath = "{$backupPath}/{$zipFilename}";

        $this->info("🚀 Iniciando backup {$type} de la base de datos '{$dbName}'...");

        // 1. Crear el dump SQL
        $command = "mysqldump --host={$dbHost} --port={$dbPort} --user={$dbUser} ";

        if (!empty($dbPass)) {
            $command .= "--password=\"{$dbPass}\" ";
        }

        $command .= "--single-transaction --quick --lock-tables=false --routines --triggers {$dbName} > \"{$sqlPath}\"";

        $result = null;
        exec($command, $output, $result);

        if ($result !== 0 || !file_exists($sqlPath)) {
            $this->error(" Error al ejecutar mysqldump.");
            return;
        }

        // 2. Comprimir el archivo SQL en ZIP
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($sqlPath, $sqlFilename);
            $zip->close();

            // Eliminar el archivo .sql temporal
            unlink($sqlPath);

            $sizeMB = round(filesize($zipPath) / (1024 * 1024), 2);
            $this->info(" Backup {$type} completado y comprimido exitosamente!");
            $this->info(" Archivo: {$zipFilename}");
            $this->info(" Tamaño: {$sizeMB} MB");
            $this->info(" Ruta: storage/app/backups/");
        } else {
            $this->error(" Error al comprimir el backup.");
        }
    }
}