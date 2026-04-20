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

    public function __construct(Collection $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;        // ← Importante: usa los registros seleccionados
    }

    public function map($contrato): array
    {
        return [
            $contrato->fecha?->format('d/m/Y') ?? '',
            $contrato->numero_contrato ?? '',
            $contrato->nombre_cliente ?? '',
            $contrato->nombre_vendedor ?? '',
            $contrato->estructura ?? '',
            $contrato->tipo_concreto ?? '',
            $contrato->ubicacion_referencia ?? '',           // Agregado
            $contrato->bombeo ?? '',

            number_format($contrato->volumen_guia ?? 0, 2),
            number_format($contrato->volumen_real ?? 0, 2),
            number_format($contrato->volumen_sobrante ?? 0, 2),

            number_format($contrato->bomba ?? 0, 2),
            number_format($contrato->bomba_adicional ?? 0, 2),

            number_format($contrato->pu ?? 0, 2),
            number_format($contrato->pu_real ?? 0, 2),
            number_format($contrato->monto_total ?? 0, 2),
            number_format($contrato->venta_neta ?? 0, 2),
            number_format($contrato->total_para_empresa ?? 0, 2),

            number_format($contrato->descuento_guias ?? 0, 2),
            number_format($contrato->descuentos ?? 0, 2),
            number_format($contrato->gastos_alimentos_bomba ?? 0, 2),
            number_format($contrato->comision ?? 0, 2),
            number_format($contrato->comision_igv ?? 0, 2),

            $contrato->estado_pago ?? '',
            $contrato->forma_pago ?? '',
            $contrato->observacion ?? '',
            $contrato->observaciones_adicionales ?? '',

            $contrato->created_at?->format('d/m/Y H:i:s') ?? '',
        ];
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'N° Contrato',
            'Cliente',
            'Vendedor',
            'Estructura',
            'Tipo Concreto',
            'Ubicación / Referencia',
            'Bombeo',
            'Vol. Guía (m³)',
            'Vol. Real (m³)',
            'Vol. Sobrante (m³)',
            'Bomba (S/)',
            'Bomba Adic. (S/)',
            'P.U. (S/)',
            'P.U. Real (S/)',
            'Monto Total (S/)',
            'Venta Neta (S/)',
            'Total Empresa (S/)',
            'Desc. Guías (S/)',
            'Descuentos (S/)',
            'Gastos Bomba (S/)',
            'Comisión (S/)',
            'Com. IGV (S/)',
            'Estado Pago',
            'Forma Pago',
            'Observación',
            'Observaciones Adicionales',
            'Fecha Registro',
        ];
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

                // Título grande
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'REPORTE DE CONTRATOS - CONCRETERA HUANCAYO');
                $sheet->mergeCells('A1:AB1');     // 28 columnas ahora

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A1C9']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Bordes en toda la tabla
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A3:AB{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);

                // Congelar encabezados
                $sheet->freezePane('A4');
            },
        ];
    }
}