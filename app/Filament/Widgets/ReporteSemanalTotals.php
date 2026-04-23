<?php

namespace App\Filament\Widgets;

use App\Models\ReporteSemanal;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ReporteSemanalTotals extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        // Totales por Semana
        $totalSemana1 = ReporteSemanal::where('tipo_periodo', 'semanal')->where('semana', 1)->sum('monto');
        $totalSemana2 = ReporteSemanal::where('tipo_periodo', 'semanal')->where('semana', 2)->sum('monto');
        $totalSemana3 = ReporteSemanal::where('tipo_periodo', 'semanal')->where('semana', 3)->sum('monto');
        $totalSemana4 = ReporteSemanal::where('tipo_periodo', 'semanal')->where('semana', 4)->sum('monto');
        $totalSemana5 = ReporteSemanal::where('tipo_periodo', 'semanal')->where('semana', 5)->sum('monto');
        $totalQuincenas = ReporteSemanal::where('tipo_periodo', 'quincena')->sum('monto');
        $totalFinMes = ReporteSemanal::where('tipo_periodo', 'fin_mes')->sum('monto');
        $totalDebe = ReporteSemanal::where('estado', 'DEBE')->sum('monto');
        $totalCancelado = ReporteSemanal::where('estado', 'CANCELADO')->sum('monto');
        $totalAdelanto = ReporteSemanal::where('estado', 'ADELANTO')->sum('monto');
        $totalGeneral = ReporteSemanal::sum('monto');
        
        return $table
            ->query(
                \App\Models\ReporteSemanal::query()
                    ->selectRaw('
                        "Semana 1" as concepto,
                        ? as total,
                        "blue" as color
                    ', [number_format($totalSemana1, 2)])
                    ->union(
                        \App\Models\ReporteSemanal::query()
                            ->selectRaw('"Semana 2" as concepto, ? as total, "blue" as color', [number_format($totalSemana2, 2)])
                    )
                    ->union(
                        \App\Models\ReporteSemanal::query()
                            ->selectRaw('"Semana 3" as concepto, ? as total, "blue" as color', [number_format($totalSemana3, 2)])
                    )
                    ->union(
                        \App\Models\ReporteSemanal::query()
                            ->selectRaw('"Semana 4" as concepto, ? as total, "blue" as color', [number_format($totalSemana4, 2)])
                    )
                    ->union(
                        \App\Models\ReporteSemanal::query()
                            ->selectRaw('"Semana 5" as concepto, ? as total, "blue" as color', [number_format($totalSemana5, 2)])
                    )
                    ->union(
                        \App\Models\ReporteSemanal::query()
                            ->selectRaw('"Quincenas" as concepto, ? as total, "green" as color', [number_format($totalQuincenas, 2)])
                    )
                    ->union(
                        \App\Models\ReporteSemanal::query()
                            ->selectRaw('"Fin de Mes" as concepto, ? as total, "yellow" as color', [number_format($totalFinMes, 2)])
                    )
                    ->union(
                        \App\Models\ReporteSemanal::query()
                            ->selectRaw('"Pendiente (Debe)" as concepto, ? as total, "red" as color', [number_format($totalDebe, 2)])
                    )
                    ->union(
                        \App\Models\ReporteSemanal::query()
                            ->selectRaw('"Cancelado" as concepto, ? as total, "green" as color', [number_format($totalCancelado, 2)])
                    )
                    ->union(
                        \App\Models\ReporteSemanal::query()
                            ->selectRaw('"Adelanto" as concepto, ? as total, "orange" as color', [number_format($totalAdelanto, 2)])
                    )
                    ->union(
                        \App\Models\ReporteSemanal::query()
                            ->selectRaw('"TOTAL GENERAL" as concepto, ? as total, "indigo" as color', [number_format($totalGeneral, 2)])
                    )
            )
            ->columns([
                Tables\Columns\TextColumn::make('concepto')
                    ->label('Concepto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Monto')
                    ->formatStateUsing(fn ($state) => "S/ {$state}"),
            ])
            ->paginated(false);
    }
}