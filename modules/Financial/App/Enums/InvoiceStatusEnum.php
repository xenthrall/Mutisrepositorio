<?php

namespace Modules\Financial\App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum InvoiceStatusEnum: string implements HasLabel, HasColor, HasIcon
{
    case Quotation = 'quotation'; // Presupuesto / Borrador
    case Order = 'order';         // Orden de Venta / Pedido confirmado
    case Invoiced = 'invoiced';   // Facturado / Publicado en contabilidad
    case Paid = 'paid';           // Pagado totalmente
    case Cancelled = 'cancelled'; // Anulado

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Quotation => 'Presupuesto',
            self::Order => 'Orden de Venta',
            self::Invoiced => 'Facturado',
            self::Paid => 'Pagado',
            self::Cancelled => 'Anulado',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Quotation => 'gray',      // Gris para borradores
            self::Order => 'info',          // Azul para órdenes en proceso
            self::Invoiced => 'warning',    // Naranja indicando que hay deuda pendiente
            self::Paid => 'success',        // Verde cuando ya entró el dinero
            self::Cancelled => 'danger',    // Rojo para anulaciones
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Quotation => 'heroicon-m-document',
            self::Order => 'heroicon-m-shopping-bag',
            self::Invoiced => 'heroicon-m-document-check',
            self::Paid => 'heroicon-m-check-circle',
            self::Cancelled => 'heroicon-m-x-circle',
        };
    }
}