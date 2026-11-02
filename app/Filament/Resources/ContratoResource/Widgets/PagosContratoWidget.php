<?php

namespace App\Filament\Resources\ContratoResource\Widgets;

use App\Models\Contrato;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;

/**
 * Franja de pagos del contrato: total, pagado, saldo y la lista de pagos (adelanto y abonos).
 */
class PagosContratoWidget extends Widget
{
    protected string $view = 'filament.resources.contrato.pagos-contrato-widget';

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public ?Model $record = null;

    // Se vuelve a dibujar cuando se registra un pago desde la página
    #[On('pago-registrado')]
    public function refrescar(): void
    {
        $this->record?->refresh();
    }

    protected function getViewData(): array
    {
        /** @var Contrato $contrato */
        $contrato = $this->record;
        $total = (float) $contrato->monto_total;
        $pagado = $contrato->totalCobrado();

        return [
            'total'   => $total,
            'pagado'  => $pagado,
            'saldo'   => $contrato->saldoPorCobrar(),
            'avance'  => $total > 0 ? min(100, round($pagado / $total * 100)) : 0,
            'pagos'   => $contrato->pagos(),
            'sinNumero' => blank($contrato->numero_contrato),
        ];
    }
}
