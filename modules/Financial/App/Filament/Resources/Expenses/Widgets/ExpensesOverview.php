<?php

namespace Modules\Financial\App\Filament\Resources\Expenses\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Financial\App\Models\Expense;

class ExpensesOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $baseQuery = Expense::query()->where('description', '!=', '[BORRADOR]');

        $todayTotal = (clone $baseQuery)->whereDate('expense_date', today())->sum('amount');
        $monthTotal = (clone $baseQuery)
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
        $monthCount = (clone $baseQuery)
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->count();
        $draftCount = Expense::query()->where('description', '[BORRADOR]')->count();

        return [
            Stat::make('Egresos Hoy', '$' . number_format((float) $todayTotal, 0, ',', '.'))
                ->description('Total registrado hoy')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('danger'),

            Stat::make('Egresos del Mes', '$' . number_format((float) $monthTotal, 0, ',', '.'))
                ->description("{$monthCount} egresos confirmados")
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('warning'),

            Stat::make('Promedio Mensual', '$' . number_format($monthCount > 0 ? (float) $monthTotal / $monthCount : 0, 0, ',', '.'))
                ->description('Por egreso confirmado')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('gray'),

            Stat::make('Borradores', (string) $draftCount)
                ->description('Pendientes de confirmar')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($draftCount > 0 ? 'info' : 'success'),
        ];
    }
}
