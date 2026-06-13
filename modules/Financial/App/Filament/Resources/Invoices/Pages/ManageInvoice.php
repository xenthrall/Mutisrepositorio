<?php

namespace Modules\Financial\App\Filament\Resources\Invoices\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;
use Modules\Core\App\Models\Partner;
use Modules\Financial\App\Enums\InvoiceStatusEnum;
use Modules\Financial\App\Filament\Resources\Invoices\InvoiceResource;
use Modules\Financial\App\Models\Journal;
use Modules\Financial\App\Models\Product;

class ManageInvoice extends Page
{
    use InteractsWithRecord;

    protected static string $resource = InvoiceResource::class;

    protected string $view = 'financial::filament.resources.invoices.pages.manage-invoice';

    protected static ?string $title = 'Punto de Venta';

    protected Width|string|null $maxContentWidth = Width::Full;

    public $partner_id;

    public $lines = [];

    public $totalAmount = 0;

    public $searchProduct = '';

    public $searchPartner = '';

    public $isPaymentModalOpen = false;

    public $payment_journal_id = '';

    public $payment_amount = 0;

    public $payment_memo = '';

    public bool $isLocked = false;

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->record->load(['lines.product', 'partner']);

        $this->partner_id = $this->record->partner_id;
        $this->isLocked = in_array($this->record->status, [
            InvoiceStatusEnum::Paid,
            InvoiceStatusEnum::Cancelled,
        ], true);

        foreach ($this->record->lines as $line) {
            $this->lines[] = [
                'product_id' => $line->product_id,
                'name' => $line->description ?? $line->product?->name ?? 'Producto',
                'quantity' => (float) $line->quantity,
                'unit_price' => (float) $line->unit_price,
                'subtotal' => (float) $line->subtotal,
            ];
        }

        $this->calculateTotal();
    }

    public function getSelectedPartnerProperty()
    {
        return $this->partner_id
            ? Partner::find($this->partner_id)
            : null;
    }

    public function getFilteredPartnersProperty()
    {
        if (strlen($this->searchPartner) < 2) {
            return [];
        }

        return Partner::query()
            ->where(function ($query) {
                $query->where('first_name', 'like', "%{$this->searchPartner}%")
                    ->orWhere('last_name', 'like', "%{$this->searchPartner}%")
                    ->orWhere('company_name', 'like', "%{$this->searchPartner}%")
                    ->orWhere('document_number', 'like', "%{$this->searchPartner}%");
            })
            ->take(10)
            ->get();
    }

    public function getProductsProperty()
    {
        return Product::where('is_active', true)
            ->when($this->searchProduct, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', "%{$this->searchProduct}%")
                        ->orWhere('code', 'like', "%{$this->searchProduct}%");
                });
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
        if ($this->isLocked) {
            return;
        }

        $this->partner_id = $id;
        $this->searchPartner = '';
    }

    public function clearPartner(): void
    {
        if ($this->isLocked) {
            return;
        }

        $this->partner_id = null;
    }

    public function addProduct($productId): void
    {
        if ($this->isLocked) {
            return;
        }

        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        foreach ($this->lines as $index => $line) {
            if ($line['product_id'] === $product->id) {
                $this->lines[$index]['quantity']++;
                $this->updateLine($index);

                return;
            }
        }

        $this->lines[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => 1,
            'unit_price' => (float) $product->price,
            'subtotal' => (float) $product->price,
        ];

        $this->calculateTotal();
    }

    public function updateLine($index): void
    {
        if ($this->isLocked) {
            return;
        }

        $qty = (float) ($this->lines[$index]['quantity'] ?: 0);
        $price = (float) ($this->lines[$index]['unit_price'] ?: 0);

        $this->lines[$index]['subtotal'] = $qty * $price;
        $this->calculateTotal();
    }

    public function removeLine($index): void
    {
        if ($this->isLocked) {
            return;
        }

        unset($this->lines[$index]);
        $this->lines = array_values($this->lines);
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        $this->totalAmount = collect($this->lines)->sum('subtotal');
    }

    public function saveInvoice($showNotification = true): bool
    {
        if ($this->isLocked) {
            Notification::make()->title('Esta factura no puede modificarse')->warning()->send();

            return false;
        }

        if (! $this->validateTicket()) {
            return false;
        }

        $this->persistInvoice(InvoiceStatusEnum::Quotation);

        if ($showNotification) {
            Notification::make()->title('Borrador guardado correctamente')->success()->send();
        }

        return true;
    }

    public function openPaymentModal(): void
    {
        if ($this->isLocked) {
            Notification::make()->title('Esta factura ya fue procesada')->warning()->send();

            return;
        }

        if (! $this->validateTicket()) {
            return;
        }

        $this->persistInvoice(InvoiceStatusEnum::Invoiced);

        $this->payment_amount = $this->totalAmount;
        $this->payment_memo = 'Pago Factura ' . $this->record->number;
        $this->isPaymentModalOpen = true;
    }

    public function closePaymentModal(): void
    {
        $this->isPaymentModalOpen = false;
        $this->resetValidation();
    }

    public function confirmPayment()
    {
        if ($this->isLocked || $this->record->status === InvoiceStatusEnum::Paid) {
            Notification::make()->title('Esta factura ya fue pagada')->warning()->send();

            return;
        }

        $this->validate([
            'payment_journal_id' => 'required',
            'payment_amount' => 'required|numeric|min:' . $this->totalAmount,
        ], [
            'payment_journal_id.required' => 'Debe seleccionar un método de pago (Diario).',
            'payment_amount.min' => 'El monto no puede ser menor al total de la factura.',
        ]);

        $journal = Journal::find($this->payment_journal_id);

        if (! $journal) {
            Notification::make()->title('Diario no encontrado')->danger()->send();

            return;
        }

        $this->record->payments()->create([
            'journal_id' => $this->payment_journal_id,
            'amount' => $this->totalAmount,
            'payment_date' => now(),
            'memo' => $this->payment_memo,
        ]);

        $this->record->update([
            'status' => InvoiceStatusEnum::Paid,
            'amount_due' => 0,
        ]);

        $journal->increment('balance', $this->totalAmount);

        $this->isPaymentModalOpen = false;
        $this->isLocked = true;

        Notification::make()
            ->title('¡Pago procesado con éxito!')
            ->success()
            ->send();

        return redirect(InvoiceResource::getUrl('index'));
    }

    protected function validateTicket(): bool
    {
        if (! $this->partner_id) {
            Notification::make()->title('Seleccione un cliente')->danger()->send();

            return false;
        }

        if (empty($this->lines)) {
            Notification::make()->title('El ticket está vacío')->danger()->send();

            return false;
        }

        if ($this->totalAmount <= 0) {
            Notification::make()->title('El total debe ser mayor a cero')->danger()->send();

            return false;
        }

        return true;
    }

    protected function persistInvoice(InvoiceStatusEnum $status): void
    {
        $this->record->update([
            'partner_id' => $this->partner_id,
            'subtotal' => $this->totalAmount,
            'total_amount' => $this->totalAmount,
            'amount_due' => $status === InvoiceStatusEnum::Paid ? 0 : $this->totalAmount,
            'status' => $status,
        ]);

        $this->record->lines()->delete();

        foreach ($this->lines as $line) {
            $this->record->lines()->create([
                'product_id' => $line['product_id'],
                'description' => $line['name'],
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'subtotal' => $line['subtotal'],
            ]);
        }

        $this->record->refresh();
    }
}
