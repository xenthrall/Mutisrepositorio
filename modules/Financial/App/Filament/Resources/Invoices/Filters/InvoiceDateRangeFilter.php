<?php

namespace Modules\Financial\App\Filament\Resources\Invoices\Filters;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class InvoiceDateRangeFilter
{
    public static function make(): Filter
    {
        return Filter::make('invoice_date_range')
            ->label('Rango de fechas')
            ->schema([
                DatePicker::make('from')
                    ->label('Desde')
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                DatePicker::make('until')
                    ->label('Hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when(
                        $data['from'] ?? null,
                        fn (Builder $query, $date): Builder => $query->whereDate('invoice_date', '>=', $date),
                    )
                    ->when(
                        $data['until'] ?? null,
                        fn (Builder $query, $date): Builder => $query->whereDate('invoice_date', '<=', $date),
                    );
            })
            ->indicateUsing(function (array $data): array {
                $indicators = [];

                if ($data['from'] ?? null) {
                    $indicators[] = 'Desde ' . Carbon::parse($data['from'])->format('d/m/Y');
                }

                if ($data['until'] ?? null) {
                    $indicators[] = 'Hasta ' . Carbon::parse($data['until'])->format('d/m/Y');
                }

                return $indicators;
            });
    }
}
