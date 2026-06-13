<?php

namespace Modules\Financial\App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ProductTypeEnum: string implements HasLabel, HasColor, HasIcon
{
    case Good = 'good';       // Producto físico (ej. Uniforme, Agenda)
    case Service = 'service'; // Actividad económica (ej. Pensión, Matrícula)

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Good => 'Producto Físico',
            self::Service => 'Servicio',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Good => 'warning',
            self::Service => 'primary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Good => 'heroicon-m-cube',
            self::Service => 'heroicon-m-sparkles', // O heroicon-m-briefcase
        };
    }
}