<?php

namespace Modules\Financial\App\Filament\Resources\Expenses\Filters;

use Filament\Tables\Filters\SelectFilter;
use Modules\Financial\App\Models\ExpenseCategory;

class ExpenseCategoryFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('expense_category_id')
            ->label('Categoría')
            ->options(fn () => ExpenseCategory::orderBy('name')->pluck('name', 'id'))
            ->searchable()
            ->preload();
    }
}
