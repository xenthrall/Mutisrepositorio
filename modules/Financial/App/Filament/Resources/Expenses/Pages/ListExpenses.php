<?php

namespace Modules\Financial\App\Filament\Resources\Expenses\Pages;

use Filament\Actions\Action;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Modules\Financial\App\Filament\Resources\Expenses\ExpenseResource;
use Modules\Financial\App\Filament\Resources\Expenses\Widgets\ExpensesOverview;
use Modules\Financial\App\Models\Expense;
use Modules\Financial\App\Models\ExpenseCategory;
use Modules\Financial\App\Models\Journal;

class ListExpenses extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pos')
                ->label('Registrar Egreso (POS)')
                ->icon('heroicon-m-computer-desktop')
                ->color('primary')
                ->size('lg')
                ->action(function () {
                    $category = ExpenseCategory::where('is_active', true)->first();
                    $journal = Journal::where('is_active', true)->first();

                    $expense = Expense::create([
                        'expense_category_id' => $category?->id ?? ExpenseCategory::first()?->id,
                        'journal_id' => $journal?->id ?? Journal::first()?->id,
                        'partner_id' => null,
                        'amount' => 0,
                        'expense_date' => now(),
                        'description' => '[BORRADOR]',
                    ]);

                    return redirect(ExpenseResource::getUrl('manage', ['record' => $expense->id]));
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ExpensesOverview::class,
        ];
    }
}
