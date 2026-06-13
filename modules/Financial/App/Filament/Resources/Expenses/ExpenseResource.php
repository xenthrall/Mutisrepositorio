<?php

namespace Modules\Financial\App\Filament\Resources\Expenses;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Financial\App\Filament\Resources\Expenses\Pages\ListExpenses;
use Modules\Financial\App\Filament\Resources\Expenses\Tables\ExpensesTable;
use Modules\Financial\App\Models\Expense;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowTrendingDown;

    protected static ?string $recordTitleAttribute = 'description';

    protected static ?string $modelLabel = 'Egreso';

    protected static ?string $pluralModelLabel = 'Egresos';

    protected static ?string $navigationLabel = 'Egresos POS';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return ExpensesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExpenses::route('/'),
            'manage' => Pages\ManageExpense::route('/{record}/manage'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
