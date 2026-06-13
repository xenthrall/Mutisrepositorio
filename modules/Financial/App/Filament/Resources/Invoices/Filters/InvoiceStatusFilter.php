<?php

namespace Modules\Financial\App\Filament\Resources\Invoices\Filters;

use Filament\Tables\Filters\SelectFilter;
use Modules\Financial\App\Enums\InvoiceStatusEnum;

class InvoiceStatusFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('status')
            ->label('Estado')
            ->options(InvoiceStatusEnum::class)
            ->multiple()
            ->preload();
    }
}
