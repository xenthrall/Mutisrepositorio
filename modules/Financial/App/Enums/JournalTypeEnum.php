<?php

namespace Modules\Financial\App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum JournalTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case Cash = 'cash';
    case Bank = 'bank';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Cash => 'Efectivo',
            self::Bank => 'Transferencia / Banco',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Cash => 'success',
            self::Bank => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Cash => 'heroicon-m-banknotes',
            self::Bank => 'heroicon-m-building-library',
        };
    }
}