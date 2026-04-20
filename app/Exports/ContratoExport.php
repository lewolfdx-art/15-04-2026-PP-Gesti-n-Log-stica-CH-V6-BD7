<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ContratoExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents, WithStyles
{
    protected $records;
    protected array $selectedColumns;   // ← Nuevo

    // Constructor modificado
    public function __construct(Collection $records, array $selectedColumns = [])
    {
        $this->records = $records;
        $this->selectedColumns = $selectedColumns;
    }

    public function collection()
    {
        return $this->records;
    }

    // Todas las columnas posibles
    private function getAllColumns(): array
    {
        return [
            'fecha'                     => ['header' => 'Fecha',                    'value' => fn($c) => $c->fecha?->format('d/m/Y') ?? ''],
            'numero_contrato'           => ['header' => 'N° Contrato',              'value' => fn($c) => $c->numero_contrato ?? ''],
            'nombre_cliente'            => ['header' => 'Cliente',                  'value' => fn($c) => $c->nombre_cliente ?? ''],
            'nombre_vendedor'           => ['header' => 'Vendedor',                 'value' => fn($c) => $c->nombre_vendedor ?? ''],
            'estructura'                => ['header' => 'Estructura',               'value' => fn($c) => $c->estructura ?? ''],
            'tipo_concreto'             => ['header' => 'Tipo Concreto',            'value' => fn($c) => $c->tipo_concreto ?? ''],
            'ubicacion_referencia'      => ['header' => 'Ubicación / Referencia',   'value' => fn($c) => $c->ubicacion_referencia ?? ''],
            'bombeo'                    => ['header' => 'Bombeo',                   'value' => fn($c) => $c->bombeo ?? ''],
            'volumen_guia'              => ['header' => 'Vol. Guía (m³)',           'value' => fn($c) => number_format($c->volumen_guia ?? 0, 2)],
            'volumen_real'              => ['header' => 'Vol. Real (m³)',           'value' => fn($c) => number_format($c->volumen_real ?? 0, 2)],
            'volumen_sobrante'          => ['header' => 'Vol. Sobrante (m³)',       'value' => fn($c) => number_format($c->volumen_sobrante ?? 0, 2)],
            'bomba'                     => ['header' => 'Bomba (S/)',               'value' => fn($c) => number_format($c->bomba ?? 0, 2)],
            'bomba_adicional'           => ['header' => 'Bomba Adic. (S/)',         'value' => fn($c) => number_format($c->bomba_adicional ?? 0, 2)],
            'pu'                        => ['header' => 'P.U. (S/)',                'value' => fn($c) => number_format($c->pu ?? 0, 2)],
            'pu_real'                   => ['header' => 'P.U. Real (S/)',           'value' => fn($c) => number_format($c->pu_real ?? 0, 2)],
            'monto_total'               => ['header' => 'Monto Total (S/)',         'value' => fn($c) => number_format($c->monto_total ?? 0, 2)],
            'venta_neta'                => ['header' => 'Venta Neta (S/)',          'value' => fn($c) => number_format($c->venta_neta ?? 0, 2)],
            'total_para_empresa'        => ['header' => 'Total Empresa (S/)',       'value' => fn($c) => number_format($c->total_para_empresa ?? 0, 2)],
            'descuento_guias'           => ['header' => 'Desc. Guías (S/)',         'value' => fn($c) => number_format($c->descuento_guias ?? 0, 2)],
            'descuentos'                => ['header' => 'Descuentos (S/)',          'value' => fn($c) => number_format($c->descuentos ?? 0, 2)],
            'gastos_alimentos_bomba'    => ['header' => 'Gastos Bomba (S/)',        'value' => fn($c) => number_format($c->gastos_alimentos_bomba ?? 0, 2)],
            'comision'                  => ['header' => 'Comisión (S/)',            'value' => fn($c) => number_format($c->comision ?? 0, 2)],
            'comision_igv'              => ['header' => 'Com. IGV (S/)',            'value' => fn($c) => number_format($c->comision_igv ?? 0, 2)],
            'estado_pago'               => ['header' => 'Estado Pago',              'value' => fn($c) => $c->estado_pago ?? ''],
            'forma_pago'                => ['header' => 'Forma Pago',               'value' => fn($c) => $c->forma_pago ?? ''],
            'observacion'               => ['header' => 'Observación',              'value' => fn($c) => $c->observacion ?? ''],
            'observaciones_adicionales' => ['header' => 'Observaciones Adicionales','value' => fn($c) => $c->observaciones_adicionales ?? ''],
            'created_at'                => ['header' => 'Fecha Registro',           'value' => fn($c) => $c->created_at?->format('d/m/Y H:i:s') ?? ''],
        ];
    }

    public function map($contrato): array
    {
        $columns = $this->getAllColumns();
        $row = [];

        $keysToExport = empty($this->selectedColumns) ? array_keys($columns) : $this->selectedColumns;

        foreach ($keysToExport as $key) {
            if (isset($columns[$key])) {
                $row[] = $columns[$key]['value']($contrato);
            }
        }

        return $row;
    }

    public function headings(): array
    {
        $columns = $this->getAllColumns();
        $headers = [];

        $keysToExport = empty($this->selectedColumns) ? array_keys($columns) : $this->selectedColumns;

        foreach ($keysToExport as $key) {
            $headers[] = $columns[$key]['header'] ?? ucfirst($key);
        }

        return $headers;
    }

    public function title(): string
    {
        return 'Reporte de Contratos';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A1C9']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            3 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A1C9']],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'REPORTE DE CONTRATOS - CONCRETERA HUANCAYO');

                $colCount = count($this->headings());
                $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

                $sheet->mergeCells("A1:{$lastColumn}1");

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A1C9']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A3:{$lastColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                $sheet->freezePane('A4');
            },
        ];
    }
}