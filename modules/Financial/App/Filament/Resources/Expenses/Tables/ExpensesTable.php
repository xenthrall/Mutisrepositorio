<?php

namespace Modules\Financial\App\Filament\Resources\Expenses\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Financial\App\Filament\Resources\Expenses\ExpenseResource;
use Modules\Financial\App\Filament\Resources\Expenses\Filters\ExpenseCategoryFilter;
use Modules\Financial\App\Filament\Resources\Expenses\Filters\ExpenseDateRangeFilter;
use Modules\Financial\App\Filament\Resources\Expenses\Filters\ExpenseJournalFilter;
use Modules\Financial\App\Filament\Resources\Expenses\Filters\ExpenseTrashedFilter;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Nº Egreso')
                    ->formatStateUsing(fn (int $state): string => 'EG-' . str_pad((string) $state, 5, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Número copiado')
                    ->weight('bold'),

                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('partner.full_name')
                    ->label('Proveedor / Beneficiario')
                    ->getStateUsing(fn ($record): string => $record->partner?->full_name ?? 'Sin tercero asignado')
                    ->searchable(['partner.first_name', 'partner.last_name', 'partner.company_name'])
                    ->sortable(['partner.first_name', 'partner.last_name']),

                TextColumn::make('description')
                    ->label('Concepto')
                    ->formatStateUsing(fn (string $state): string => $state === '[BORRADOR]' ? 'Borrador' : $state)
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record): ?string => $record->description !== '[BORRADOR]' ? $record->description : null),

                TextColumn::make('expense_date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('journal.name')
                    ->label('Caja / Banco')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->money('COP')
                    ->sortable()
                    ->alignment('right')
                    ->weight('bold')
                    ->color('danger'),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                ExpenseCategoryFilter::make(),
                ExpenseJournalFilter::make(),
                ExpenseDateRangeFilter::make(),
                ExpenseTrashedFilter::make(),
            ])
            ->defaultSort('expense_date', 'desc')
            ->recordUrl(
                fn ($record): string => ExpenseResource::getUrl('manage', ['record' => $record])
            )
            ->openRecordUrlInNewTab(false);
    }
}
