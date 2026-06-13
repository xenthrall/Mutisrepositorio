<?php

namespace Modules\Financial\App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Modules\Financial\App\Models\ExpenseCategory;
use Modules\Financial\App\Models\Journal;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('expense_category_id')
                    ->label('Categoría')
                    ->options(fn () => ExpenseCategory::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('journal_id')
                    ->label('Caja / Banco')
                    ->options(fn () => Journal::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('partner_id')
                    ->label('Proveedor / Beneficiario')
                    ->relationship('partner', 'first_name')
                    ->searchable()
                    ->nullable(),

                TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),

                DatePicker::make('expense_date')
                    ->label('Fecha del egreso')
                    ->required(),

                Textarea::make('description')
                    ->label('Concepto')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
