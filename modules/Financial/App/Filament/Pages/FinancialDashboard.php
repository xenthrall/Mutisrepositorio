<?php

namespace Modules\Financial\App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Modules\Core\App\Models\Partner;
use Modules\Financial\App\Models\Expense;
use Modules\Financial\App\Models\Invoice;
use Modules\Financial\App\Models\Journal;
use Modules\Financial\App\Models\Payment;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class FinancialDashboard extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Flujo de Caja';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartLine;

    protected static ?int $navigationSort = 4;
    
    protected string $view = 'financial::filament.pages.dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()->can('financial.dashboard.view');
    }
    
    // --- Persistencia en URL (Livewire 3) ---
    #[Url(as: 'desde', history: true)]
    public string $startDate = '';

    #[Url(as: 'hasta', history: true)]
    public string $endDate = '';

    #[Url(as: 'diario', history: true)]
    public string $filterJournalId = '';

    #[Url(as: 'tercero', history: true)]
    public string $filterPartnerId = '';

    #[Url(as: 'pestana', history: true)]
    public string $activeTab = 'overview';

    // --- Rangos rápidos ---
    public function setRange(string $range): void
    {
        match ($range) {
            'today' => [$this->startDate, $this->endDate] = [now()->format('Y-m-d'), now()->format('Y-m-d')],
            'week' => [$this->startDate, $this->endDate] = [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')],
            'month' => [$this->startDate, $this->endDate] = [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')],
            'quarter' => [$this->startDate, $this->endDate] = [now()->startOfQuarter()->format('Y-m-d'), now()->endOfQuarter()->format('Y-m-d')],
            'year' => [$this->startDate, $this->endDate] = [now()->startOfYear()->format('Y-m-d'), now()->endOfYear()->format('Y-m-d')],
            default => [$this->startDate, $this->endDate] = ['', ''],
        };
    }

    public function resetFilters(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->filterJournalId = '';
        $this->filterPartnerId = '';
    }

    public function mount(): void
    {
        if (empty($this->startDate)) {
            $this->startDate = now()->startOfMonth()->format('Y-m-d');
        }
        if (empty($this->endDate)) {
            $this->endDate = now()->endOfMonth()->format('Y-m-d');
        }
    }

    // --- Formulario de filtros (más elegante) ---
    public function filterForm(Schema $form): Schema
    {
        return $form->schema([
            DatePicker::make('startDate')
                ->label('Desde')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->live(),
            DatePicker::make('endDate')
                ->label('Hasta')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->live(),
            Select::make('filterJournalId')
                ->label('Caja / Banco')
                ->options(fn() => Journal::where('is_active', true)->pluck('name', 'id'))
                ->searchable()
                ->placeholder('Todas...')
                ->live(),
            Select::make('filterPartnerId')
                ->label('Cliente / Proveedor')
                ->options(fn() => Partner::orderBy('first_name')->pluck('full_name', 'id'))
                ->searchable()
                ->placeholder('Todos...')
                ->live(),
        ]);
    }

    // --- Queries base reutilizables ---
    private function paymentQuery(): Builder
    {
        return Payment::query()
            ->when($this->startDate, fn($q) => $q->whereDate('payment_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('payment_date', '<=', $this->endDate))
            ->when($this->filterJournalId, fn($q) => $q->where('journal_id', $this->filterJournalId))
            ->when($this->filterPartnerId, function ($q) {
                $q->whereHas('invoice', fn($inv) => $inv->where('partner_id', $this->filterPartnerId));
            });
    }

    private function expenseQuery(): Builder
    {
        return Expense::query()
            ->when($this->startDate, fn($q) => $q->whereDate('expense_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('expense_date', '<=', $this->endDate))
            ->when($this->filterJournalId, fn($q) => $q->where('journal_id', $this->filterJournalId))
            ->when($this->filterPartnerId, fn($q) => $q->where('partner_id', $this->filterPartnerId));
    }

    private function invoiceQuery(): Builder
    {
        return Invoice::query()
            ->when($this->startDate, fn($q) => $q->whereDate('invoice_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('invoice_date', '<=', $this->endDate))
            ->when($this->filterPartnerId, fn($q) => $q->where('partner_id', $this->filterPartnerId));
    }

    // --- KPIs ---
    public function getTotalCollectedProperty(): float
    {
        return (float) $this->paymentQuery()->sum('amount');
    }

    public function getTotalExpensesProperty(): float
    {
        return (float) $this->expenseQuery()->sum('amount');
    }

    public function getNetCashFlowProperty(): float
    {
        return $this->totalCollected - $this->totalExpenses;
    }

    public function getTotalPendingProperty(): float
    {
        return (float) $this->invoiceQuery()
            ->where('status', '!=', 'cancelled')
            ->where('amount_due', '>', 0)
            ->sum('amount_due');
    }

    public function getTotalInvoicedProperty(): float
    {
        return (float) $this->invoiceQuery()
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');
    }

    public function getCollectionRateProperty(): float
    {
        $invoiced = $this->totalInvoiced;
        return $invoiced > 0 ? round(($this->totalCollected / $invoiced) * 100, 1) : 0;
    }

    public function getOverdueInvoicesProperty(): float
    {
        return (float) $this->invoiceQuery()
            ->where('status', '!=', 'cancelled')
            ->where('amount_due', '>', 0)
            ->whereDate('due_date', '<', now())
            ->sum('amount_due');
    }

    public function getOverdueCountProperty(): int
    {
        return $this->invoiceQuery()
            ->where('status', '!=', 'cancelled')
            ->where('amount_due', '>', 0)
            ->whereDate('due_date', '<', now())
            ->count();
    }

    // --- Datos para Gráficos ---

    /**
     * Tendencia diaria de Ingresos vs Egresos (últimos 30 días del rango o rango completo)
     */
    public function getDailyFlowDataProperty(): array
    {
        $days = collect();
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $diff = $start->diffInDays($end);

        // Si el rango es muy grande, agrupamos por semana o mes. Aquí por día si <= 60 días
        $groupBy = $diff > 60 ? 'Y-m' : 'Y-m-d';
        $format = $diff > 60 ? 'M Y' : 'd M';

        $payments = $this->paymentQuery()
            ->select(DB::raw("DATE_FORMAT(payment_date, '{$groupBy}') as period"), DB::raw('SUM(amount) as total'))
            ->groupBy('period')
            ->pluck('total', 'period');

        $expenses = $this->expenseQuery()
            ->select(DB::raw("DATE_FORMAT(expense_date, '{$groupBy}') as period"), DB::raw('SUM(amount) as total'))
            ->groupBy('period')
            ->pluck('total', 'period');

        $current = $start->copy();
        while ($current <= $end) {
            $period = $current->format($groupBy === 'Y-m-d' ? 'Y-m-d' : 'Y-m');
            $days->push([
                'label' => $current->format($format),
                'period' => $period,
                'income' => (float) ($payments[$period] ?? 0),
                'expense' => (float) ($expenses[$period] ?? 0),
            ]);
            $current->add($groupBy === 'Y-m-d' ? '1 day' : '1 month');
        }

        return [
            'labels' => $days->pluck('label')->toArray(),
            'income' => $days->pluck('income')->toArray(),
            'expense' => $days->pluck('expense')->toArray(),
        ];
    }

    /**
     * Distribución de Gastos por Categoría
     */
    public function getExpensesByCategoryDataProperty(): array
    {
        $data = $this->expenseQuery()
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->with('category:id,name')
            ->groupBy('expense_category_id')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $data->pluck('category.name')->map(fn($n) => $n ?? 'Sin categoría')->toArray(),
            'values' => $data->pluck('total')->toArray(),
            'colors' => ['#10b981', '#f59e0b', '#ef4444', '#6366f1', '#8b5cf6', '#ec4899', '#14b8a6'],
        ];
    }

    /**
     * Estado de Facturas (Pagadas, Pendientes, Vencidas, Canceladas)
     */
    public function getInvoiceStatusDataProperty(): array
    {
        $query = $this->invoiceQuery();

        return [
            'paid' => (float) $query->clone()->where('status', 'paid')->sum('total_amount'),
            'pending' => (float) $query->clone()->where('status', 'pending')->whereDate('due_date', '>=', now())->sum('amount_due'),
            'overdue' => (float) $query->clone()->where('status', 'pending')->whereDate('due_date', '<', now())->sum('amount_due'),
            'cancelled' => (float) $query->clone()->where('status', 'cancelled')->sum('total_amount'),
        ];
    }

    /**
     * Top 5 Clientes por Ingresos
     */
    public function getTopPartnersDataProperty(): array
    {
        $data = Payment::query()
            ->select('invoices.partner_id', DB::raw('SUM(payments.amount) as total'))
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->when($this->startDate, fn($q) => $q->whereDate('payments.payment_date', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('payments.payment_date', '<=', $this->endDate))
            ->when($this->filterJournalId, fn($q) => $q->where('payments.journal_id', $this->filterJournalId))
            ->when($this->filterPartnerId, fn($q) => $q->where('invoices.partner_id', $this->filterPartnerId))
            ->groupBy('invoices.partner_id')
            ->with('invoice.partner:id,first_name,last_name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'labels' => $data->map(fn($item) => $item->invoice->partner->full_name ?? 'Desconocido')->toArray(),
            'values' => $data->pluck('total')->toArray(),
        ];
    }

    /**
     * Balance por Diario (Caja/Banco)
     */
    public function getJournalBalancesProperty(): array
    {
        return Journal::where('is_active', true)
            ->when($this->filterJournalId, fn($q) => $q->where('id', $this->filterJournalId))
            ->get()
            ->map(fn($j) => [
                'name' => $j->name,
                'balance' => (float) $j->balance,
                'type' => $j->type->value ?? 'general',
            ])
            ->toArray();
    }

    // --- Transacciones Paginadas ---
    public function getTransactionsProperty(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $payments = $this->paymentQuery()
            ->with(['invoice.partner', 'journal'])
            ->get()
            ->map(fn($p) => (object)[
                'id' => 'P-' . $p->id,
                'date' => $p->payment_date,
                'type' => 'in',
                'reference' => 'Factura ' . ($p->invoice->number ?? 'N/A') . ($p->memo ? ' - ' . $p->memo : ''),
                'partner' => $p->invoice->partner->full_name ?? 'S/N',
                'journal' => $p->journal->name ?? 'N/A',
                'amount' => (float) $p->amount,
                'raw' => $p,
            ]);

        $expenses = $this->expenseQuery()
            ->with(['partner', 'journal', 'category'])
            ->get()
            ->map(fn($e) => (object)[
                'id' => 'E-' . $e->id,
                'date' => $e->expense_date,
                'type' => 'out',
                'reference' => ($e->category->name ?? 'Gasto') . ': ' . $e->description,
                'partner' => $e->partner->full_name ?? 'S/N',
                'journal' => $e->journal->name ?? 'N/A',
                'amount' => (float) $e->amount,
                'raw' => $e,
            ]);

        $merged = $payments->concat($expenses)->sortByDesc('date')->values();

        // Paginación manual sobre colección
        $page = request()->get('page', 1);
        $perPage = 15;

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $merged->forPage($page, $perPage),
            $merged->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'page']
        );
    }

    // --- Facturas con detalle ---
    public function getInvoicesProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->invoiceQuery()
            ->with('partner:id,first_name,last_name')
            ->withSum('payments as total_paid', 'amount')
            ->orderByRaw("FIELD(status, 'pending', 'partial', 'paid', 'cancelled')")
            ->orderBy('due_date', 'asc')
            ->paginate(10, ['*'], 'invoicesPage');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}