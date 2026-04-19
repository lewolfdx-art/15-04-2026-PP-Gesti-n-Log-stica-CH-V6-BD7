<?php

namespace App\Filament\Exports;

use App\Models\Contrato;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ContratoExporter extends Exporter
{
    protected static ?string $model = Contrato::class;

    /**
     * Usamos el disco 'public' para que el archivo se pueda descargar fácilmente
     */
    public function getFileDisk(): string
    {
        return 'public';
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('fecha')->label('Fecha'),
            ExportColumn::make('numero_contrato')->label('N° Contrato'),
            ExportColumn::make('nombre_cliente')->label('Cliente'),
            ExportColumn::make('nombre_vendedor')->label('Vendedor'),
            ExportColumn::make('estructura')->label('Estructura'),
            ExportColumn::make('tipo_concreto')->label('Tipo Concreto'),
            ExportColumn::make('volumen_real')->label('Volumen Real (m³)'),
            ExportColumn::make('pu')->label('Precio Unitario'),
            ExportColumn::make('monto_total')->label('Monto Total'),
            ExportColumn::make('venta_neta')->label('Venta Neta'),
            ExportColumn::make('estado_pago')->label('Estado de Pago'),
            ExportColumn::make('bombeo')->label('Bombeo'),
            ExportColumn::make('created_at')->label('Fecha de Registro'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Exportación completada: ' . number_format($export->successful_rows) . ' registros exportados.';
    }
}