<?php

namespace Modules\Financial\App\Filament\Resources\Invoices\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Financial\App\Enums\InvoiceStatusEnum;
use Modules\Financial\App\Models\Invoice;
use Modules\Financial\App\Models\Payment;

class InvoicesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $todayCollected = (float) Payment::query()
            ->whereDate('payment_date', today())
            ->sum('amount');

        $monthInvoiced = (float) Invoice::query()
            ->whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->where('status', '!=', InvoiceStatusEnum::Cancelled)
            ->sum('total_amount');

        $pendingDue = (float) Invoice::query()
            ->whereIn('status', [
                InvoiceStatusEnum::Order,
                InvoiceStatusEnum::Invoiced,
            ])
            ->sum('amount_due');

        $draftCount = Invoice::query()
            ->where('status', InvoiceStatusEnum::Quotation)
            ->count();

        return [
            Stat::make('Recaudado Hoy', '$' . number_format($todayCollected, 0, ',', '.'))
                ->description('Pagos registrados hoy')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Facturado del Mes', '$' . number_format($monthInvoiced, 0, ',', '.'))
                ->description('Total emitido en el mes')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('primary'),

            Stat::make('Por Cobrar', '$' . number_format($pendingDue, 0, ',', '.'))
                ->description('Saldo pendiente de facturas')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingDue > 0 ? 'warning' : 'success'),

            Stat::make('Presupuestos', (string) $draftCount)
                ->description('Borradores sin confirmar')
                ->descriptionIcon('heroicon-m-document')
                ->color($draftCount > 0 ? 'gray' : 'success'),
        ];
    }
}
