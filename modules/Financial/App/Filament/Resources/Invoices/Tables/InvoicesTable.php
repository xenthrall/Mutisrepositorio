<?php

namespace Modules\Financial\App\Filament\Resources\Invoices\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Financial\App\Enums\InvoiceStatusEnum;
use Modules\Financial\App\Filament\Resources\Invoices\Filters\InvoiceDateRangeFilter;
use Modules\Financial\App\Filament\Resources\Invoices\Filters\InvoiceStatusFilter;
use Modules\Financial\App\Filament\Resources\Invoices\Filters\InvoiceTrashedFilter;
use Modules\Financial\App\Filament\Resources\Invoices\InvoiceResource;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Nº Factura')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Número copiado')
                    ->weight('bold'),

                TextColumn::make('partner.full_name')
                    ->label('Cliente')
                    ->getStateUsing(fn ($record): string => $record->partner?->full_name ?? 'Cliente no asignado')
                    ->searchable(['partner.first_name', 'partner.last_name', 'partner.company_name'])
                    ->sortable(['partner.first_name', 'partner.last_name']),

                TextColumn::make('invoice_date')
                    ->label('Fecha Factura')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('COP')
                    ->sortable()
                    ->alignment('right')
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('amount_due')
                    ->label('Saldo Pendiente')
                    ->money('COP')
                    ->sortable()
                    ->alignment('right')
                    ->color(fn ($record): string => (float) $record->amount_due > 0 ? 'warning' : 'gray')
                    ->weight(fn ($record): string => (float) $record->amount_due > 0 ? 'bold' : 'normal'),

                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('COP')
                    ->sortable()
                    ->alignment('right')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('memo')
                    ->label('Observaciones')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                InvoiceStatusFilter::make(),
                InvoiceDateRangeFilter::make(),
                InvoiceTrashedFilter::make(),
            ])
            ->defaultSort('invoice_date', 'desc')
            ->recordUrl(
                fn ($record): string => InvoiceResource::getUrl('manage', ['record' => $record])
            )
            ->openRecordUrlInNewTab(false);
    }
}
