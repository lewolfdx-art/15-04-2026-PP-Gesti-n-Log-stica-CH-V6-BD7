<?php

namespace App\Exports;

use App\Models\Contrato;
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
    public function collection()
    {
        return Contrato::all();
    }

    public function map($contrato): array
    {
        return [
            $contrato->fecha?->format('d/m/Y') ?? '',
            $contrato->numero_contrato,
            $contrato->nombre_cliente,
            $contrato->nombre_vendedor,
            $contrato->estructura,
            $contrato->tipo_concreto,
            number_format($contrato->volumen_real, 2),
            number_format($contrato->pu, 2),
            number_format($contrato->monto_total, 2),
            number_format($contrato->venta_neta, 2),
            $contrato->estado_pago,
            $contrato->bombeo,
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
            'Volumen Real (m³)',
            'Precio Unitario',
            'Monto Total',
            'Venta Neta',
            'Estado de Pago',
            'Bombeo',
            'Fecha de Registro',
        ];
    }

    public function title(): string
    {
        return 'Reporte de Contratos';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Título grande (fila 1)
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A1C9']], // Cyan
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],

            // Encabezados (fila 3)
            3 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A1C9']], // Cyan
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

                // Insertar filas para título
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', 'REPORTE DE CONTRATOS - CONCRETERA HUANCAYO');
                $sheet->mergeCells('A1:M1');   // Merge según tus 13 columnas

                // Estilo adicional para título
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00A1C9']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Bordes en toda la tabla
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A3:M{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                    ],
                ]);
            },
        ];
    }
}