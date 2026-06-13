<?php

namespace Modules\Financial\App\Filament\Resources\Expenses\Filters;

use Filament\Tables\Filters\TrashedFilter;

class ExpenseTrashedFilter
{
    public static function make(): TrashedFilter
    {
        return TrashedFilter::make();
    }
}
