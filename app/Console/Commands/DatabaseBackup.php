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
        $dbPort = Config::get('database.connections.mysql.port') ?? '3307';
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

        // 1. Crear el dump SQL (versión mejorada)
        $mysqldumpPath = "D:\\xampp\\mysql\\bin\\mysqldump";
        
        // Construir comando base
        $command = "\"{$mysqldumpPath}\" --host={$dbHost} --port={$dbPort} --user={$dbUser} ";
        
        // Manejar contraseña
        if (empty($dbPass)) {
            $command .= "--password= ";
        } else {
            $command .= "--password=\"{$dbPass}\" ";
        }
        
        // Agregar opciones para backup completo
        $command .= "--single-transaction --quick --lock-tables=false --routines --triggers --complete-insert --extended-insert=FALSE {$dbName}";
        
        // Redirigir salida al archivo SQL
        $command .= " > \"{$sqlPath}\" 2>&1";
        
        $this->info("Ejecutando mysqldump...");
        
        // Ejecutar comando
        $result = null;
        exec($command, $output, $result);
        
        // Verificar si el archivo se creó y tiene contenido
        if (!file_exists($sqlPath)) {
            $this->error("❌ No se pudo crear el archivo SQL");
            $this->error("Output: " . implode("\n", $output));
            return;
        }
        
        $sqlSize = filesize($sqlPath);
        if ($sqlSize < 10240) { // Menos de 10KB
            $this->warn("⚠️ El archivo SQL es muy pequeño ({$sqlSize} bytes). Posible error.");
            $this->info("Contenido del error:");
            system("type \"{$sqlPath}\"");
            return;
        }
        
        $this->info("✅ Archivo SQL creado: " . round($sqlSize / 1024, 2) . " KB");
        
        // 2. Comprimir el archivo SQL en ZIP
        $this->info("Comprimiendo backup...");
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($sqlPath, $sqlFilename);
            $zip->close();
            
            // Eliminar el archivo .sql temporal
            unlink($sqlPath);
            
            $zipSize = filesize($zipPath);
            $compressionRatio = round((1 - ($zipSize / $sqlSize)) * 100, 2);
            
            $this->info("✅ Backup {$type} completado exitosamente!");
            $this->info("📁 Archivo: {$zipFilename}");
            $this->info("📊 Tamaño SQL original: " . round($sqlSize / 1024, 2) . " KB");
            $this->info("🗜️ Tamaño ZIP: " . round($zipSize / 1024, 2) . " KB");
            $this->info("📈 Compresión: {$compressionRatio}%");
            $this->info("💾 Ruta: " . realpath($backupPath));
        } else {
            $this->error("❌ Error al comprimir el backup.");
        }
    }
}