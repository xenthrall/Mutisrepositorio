<?php

namespace Modules\Financial\App\Filament\Resources\Invoices\Pages;

use Filament\Actions\Action;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Modules\Financial\App\Enums\InvoiceStatusEnum;
use Modules\Financial\App\Filament\Resources\Invoices\InvoiceResource;
use Modules\Financial\App\Filament\Resources\Invoices\Widgets\InvoicesOverview;
use Modules\Financial\App\Models\Invoice;

class ListInvoices extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pos')
                ->label('Facturar (POS)')
                ->icon('heroicon-m-computer-desktop')
                ->color('primary')
                ->size('lg')
                ->action(function () {
                    $invoice = Invoice::create([
                        'partner_id' => null,
                        'invoice_date' => now(),
                        'status' => InvoiceStatusEnum::Quotation,
                    ]);

                    return redirect(InvoiceResource::getUrl('manage', ['record' => $invoice->id]));
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InvoicesOverview::class,
        ];
    }
}
