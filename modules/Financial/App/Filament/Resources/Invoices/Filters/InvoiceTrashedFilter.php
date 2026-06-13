<?php

namespace Modules\Financial\App\Filament\Resources\Invoices\Filters;

use Filament\Tables\Filters\TrashedFilter;

class InvoiceTrashedFilter
{
    public static function make(): TrashedFilter
    {
        return TrashedFilter::make();
    }
}
