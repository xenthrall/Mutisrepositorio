<?php

namespace Modules\Financial\App\Filament\Resources\Expenses\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Modules\Core\App\Models\Partner;
use Modules\Financial\App\Filament\Resources\Expenses\ExpenseResource;
use Modules\Financial\App\Models\ExpenseCategory;
use Modules\Financial\App\Models\Journal;

class ManageExpense extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ExpenseResource::class;

    protected string $view = 'financial::filament.resources.expenses.pages.manage-expense';

    protected static ?string $title = 'Punto de Egresos';

    protected Width|string|null $maxContentWidth = Width::Full;

    public const DRAFT_MARKER = '[BORRADOR]';

    public $partner_id;
    public $expense_category_id;
    public $description = '';
    public $amount = 0;
    public $expense_date;

    public $searchPartner = '';
    public $searchCategory = '';

    public $isPaymentModalOpen = false;
    public $payment_journal_id = '';
    public $payment_amount = 0;
    public $payment_memo = '';

    public bool $isDraft = true;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->partner_id = $this->record->partner_id;
        $this->expense_category_id = $this->record->expense_category_id;
        $this->description = $this->record->description === self::DRAFT_MARKER ? '' : $this->record->description;
        $this->amount = (float) $this->record->amount;
        $this->expense_date = $this->record->expense_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->isDraft = $this->record->description === self::DRAFT_MARKER;
    }

    public function getSelectedPartnerProperty()
    {
        return $this->partner_id
            ? Partner::find($this->partner_id)
            : null;
    }

    public function getSelectedCategoryProperty()
    {
        return $this->expense_category_id
            ? ExpenseCategory::find($this->expense_category_id)
            : null;
    }

    public function getFilteredPartnersProperty()
    {
        if (strlen($this->searchPartner) < 2) {
            return [];
        }

        return Partner::where('first_name', 'like', "%{$this->searchPartner}%")
            ->orWhere('last_name', 'like', "%{$this->searchPartner}%")
            ->orWhere('company_name', 'like', "%{$this->searchPartner}%")
            ->orWhere('document_number', 'like', "%{$this->searchPartner}%")
            ->take(10)
            ->get();
    }

    public function getCategoriesProperty()
    {
        return ExpenseCategory::where('is_active', true)
            ->when($this->searchCategory, function ($query) {
                $query->where('name', 'like', "%{$this->searchCategory}%")
                    ->orWhere('description', 'like', "%{$this->searchCategory}%");
            })
            ->orderBy('name')
            ->get();
    }

    public function getJournalsProperty()
    {
        return Journal::where('is_active', true)->get();
    }

    public function selectPartner($id): void
    {
        $this->partner_id = $id;
        $this->searchPartner = '';
    }

    public function clearPartner(): void
    {
        $this->partner_id = null;
    }

    public function selectCategory($id): void
    {
        $this->expense_category_id = $id;
    }

    public function saveExpense($showNotification = true): bool
    {
        if (! $this->expense_category_id) {
            Notification::make()->title('Seleccione una categoría de gasto')->danger()->send();

            return false;
        }

        $description = trim($this->description);

        if ($description === '') {
            $description = self::DRAFT_MARKER;
        }

        $this->record->update([
            'partner_id' => $this->partner_id,
            'expense_category_id' => $this->expense_category_id,
            'amount' => (float) ($this->amount ?: 0),
            'expense_date' => $this->expense_date,
            'description' => $description,
        ]);

        if ($showNotification) {
            Notification::make()->title('Borrador guardado correctamente')->success()->send();
        }

        return true;
    }

    public function openPaymentModal(): void
    {
        if (! $this->validateExpenseFields()) {
            return;
        }

        if ($this->saveExpense(false)) {
            $this->payment_amount = (float) $this->amount;
            $this->payment_journal_id = $this->record->journal_id;
            $this->payment_memo = 'Egreso EG-' . str_pad((string) $this->record->id, 5, '0', STR_PAD_LEFT);
            $this->isPaymentModalOpen = true;
        }
    }

    public function closePaymentModal(): void
    {
        $this->isPaymentModalOpen = false;
        $this->resetValidation();
    }

    public function confirmPayment()
    {
        if (! $this->isDraft) {
            Notification::make()->title('Este egreso ya fue confirmado')->warning()->send();

            return;
        }

        $this->validate([
            'payment_journal_id' => 'required',
            'payment_amount' => 'required|numeric|min:' . $this->amount,
        ], [
            'payment_journal_id.required' => 'Debe seleccionar la caja o banco de origen.',
            'payment_amount.min' => 'El monto no puede ser menor al total del egreso.',
        ]);

        $journal = Journal::find($this->payment_journal_id);

        if (! $journal) {
            Notification::make()->title('Diario no encontrado')->danger()->send();

            return;
        }

        if ((float) $journal->balance < (float) $this->amount) {
            Notification::make()
                ->title('Saldo insuficiente en ' . $journal->name)
                ->body('Saldo disponible: $' . number_format((float) $journal->balance, 2))
                ->danger()
                ->send();

            return;
        }

        $this->record->update([
            'journal_id' => $this->payment_journal_id,
            'description' => trim($this->description),
            'amount' => (float) $this->amount,
        ]);

        $journal->decrement('balance', (float) $this->amount);

        $this->isPaymentModalOpen = false;
        $this->isDraft = false;

        Notification::make()
            ->title('¡Egreso procesado con éxito!')
            ->success()
            ->send();

        return redirect(ExpenseResource::getUrl('index'));
    }

    protected function validateExpenseFields(): bool
    {
        if (! $this->expense_category_id) {
            Notification::make()->title('Seleccione una categoría de gasto')->danger()->send();

            return false;
        }

        if (trim($this->description) === '') {
            Notification::make()->title('Ingrese el concepto del egreso')->danger()->send();

            return false;
        }

        if ((float) $this->amount <= 0) {
            Notification::make()->title('El monto debe ser mayor a cero')->danger()->send();

            return false;
        }

        if (! $this->expense_date) {
            Notification::make()->title('Seleccione la fecha del egreso')->danger()->send();

            return false;
        }

        return true;
    }
}
