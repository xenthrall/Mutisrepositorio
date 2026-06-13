<?php

namespace Modules\Financial\App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Modules\Financial\App\Enums\InvoiceStatusEnum;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('partner_id')
                    ->label('Cliente')
                    ->relationship('partner', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->searchable(['first_name', 'last_name', 'company_name', 'document_number'])
                    ->required(),

                TextInput::make('number')
                    ->label('Número')
                    ->disabled(),

                TextInput::make('memo')
                    ->label('Observaciones'),

                DatePicker::make('invoice_date')
                    ->label('Fecha de factura')
                    ->required(),

                DatePicker::make('due_date')
                    ->label('Fecha de vencimiento'),

                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),

                TextInput::make('total_amount')
                    ->label('Total')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),

                TextInput::make('amount_due')
                    ->label('Saldo pendiente')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),

                Select::make('status')
                    ->label('Estado')
                    ->options(InvoiceStatusEnum::class)
                    ->default(InvoiceStatusEnum::Quotation)
                    ->required(),

                Textarea::make('notes')
                    ->label('Notas internas')
                    ->columnSpanFull(),
            ]);
    }
}
