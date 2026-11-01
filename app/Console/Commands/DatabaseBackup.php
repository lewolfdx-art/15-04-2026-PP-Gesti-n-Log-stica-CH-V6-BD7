<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;
use ZipArchive;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup {--monthly}';
    protected $description = 'Crea backup de la base de datos MySQL (diario o mensual) con compresión';

    public function handle(): int
    {
        $isMonthly = $this->option('monthly');

        $dbHost = Config::get('database.connections.mysql.host');
        $dbPort = Config::get('database.connections.mysql.port') ?? '3306';
        $dbName = Config::get('database.connections.mysql.database');
        $dbUser = Config::get('database.connections.mysql.username');
        $dbPass = (string) Config::get('database.connections.mysql.password');

        // En XAMPP no está en el PATH: se configura con MYSQLDUMP_PATH en el .env
        $mysqldump = Config::get('database.connections.mysql.mysqldump_path', 'mysqldump');

        $date = Carbon::now()->format('Y-m-d_H-i-s');
        $type = $isMonthly ? 'MENSUAL' : 'DIARIO';

        $backupPath = storage_path('app/backups');
        if (! file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $sqlFilename = "backup_{$type}_{$dbName}_{$date}.sql";
        $zipFilename = "backup_{$type}_{$dbName}_{$date}.zip";

        $sqlPath = "{$backupPath}/{$sqlFilename}";
        $zipPath = "{$backupPath}/{$zipFilename}";

        $this->info("Iniciando backup {$type} de la base de datos '{$dbName}'...");

        // 1. Crear el dump SQL
        // Argumentos como lista (sin pasar por la consola) y contraseña por variable de entorno,
        // para que no aparezca en la lista de procesos ni rompa el comando si tiene comillas
        $process = new Process([
            $mysqldump,
            "--host={$dbHost}",
            "--port={$dbPort}",
            "--user={$dbUser}",
            '--single-transaction',
            '--quick',
            '--lock-tables=false',
            '--routines',
            '--triggers',
            '--complete-insert',
            '--extended-insert=FALSE',
            $dbName,
        ], null, ['MYSQL_PWD' => $dbPass]);
        $process->setTimeout(600);

        $this->info('Ejecutando mysqldump...');

        // Solo la salida estándar va al archivo: los avisos y errores quedan aparte
        $archivo = fopen($sqlPath, 'w');
        $errores = '';

        try {
            $process->run(function (string $tipo, string $buffer) use ($archivo, &$errores) {
                if ($tipo === Process::OUT) {
                    fwrite($archivo, $buffer);
                } else {
                    $errores .= $buffer;
                }
            });
        } catch (\Throwable $e) {
            $errores .= $e->getMessage();
        } finally {
            fclose($archivo);
        }

        if (! $process->isSuccessful()) {
            @unlink($sqlPath);
            $this->error('mysqldump falló: ' . trim($errores ?: 'código ' . $process->getExitCode()));
            $this->error("Revisa MYSQLDUMP_PATH en el .env (actual: {$mysqldump})");

            return self::FAILURE;
        }

        $sqlSize = filesize($sqlPath);
        if ($sqlSize === 0) {
            @unlink($sqlPath);
            $this->error('El respaldo quedó vacío.');

            return self::FAILURE;
        }

        $this->info('Archivo SQL creado: ' . round($sqlSize / 1024, 2) . ' KB');

        // 2. Comprimir el archivo SQL en ZIP
        $this->info('Comprimiendo backup...');
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error("Error al comprimir el backup. El SQL quedó en {$sqlFilename}");

            return self::FAILURE;
        }

        $zip->addFile($sqlPath, $sqlFilename);
        $zip->close();

        // Eliminar el archivo .sql temporal
        unlink($sqlPath);

        $zipSize = filesize($zipPath);
        $compressionRatio = round((1 - ($zipSize / $sqlSize)) * 100, 2);

        $this->info("Backup {$type} completado exitosamente!");
        $this->info("Archivo: {$zipFilename}");
        $this->info('Tamaño SQL original: ' . round($sqlSize / 1024, 2) . ' KB');
        $this->info('Tamaño ZIP: ' . round($zipSize / 1024, 2) . ' KB');
        $this->info("Compresión: {$compressionRatio}%");
        $this->info('Ruta: ' . realpath($backupPath));

        return self::SUCCESS;
    }
}
