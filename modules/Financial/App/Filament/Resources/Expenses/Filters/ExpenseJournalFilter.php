<?php

namespace Modules\Financial\App\Filament\Resources\Expenses\Filters;

use Filament\Tables\Filters\SelectFilter;
use Modules\Financial\App\Models\Journal;

class ExpenseJournalFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('journal_id')
            ->label('Caja / Banco')
            ->options(fn () => Journal::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
            ->searchable()
            ->preload();
    }
}
