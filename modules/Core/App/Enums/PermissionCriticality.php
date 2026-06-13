<?php

namespace Modules\Core\App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PermissionCriticality: string implements HasLabel, HasColor, HasIcon
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';

    // Implementación de HasLabel
    public function getLabel(): ?string
    {
        return match ($this) {
            self::LOW => 'Bajo',
            self::MEDIUM => 'Medio',
            self::HIGH => 'Alto',
            self::CRITICAL => 'Crítico',
        };
    }

    // Implementación de HasColor
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::LOW => 'gray',
            self::MEDIUM => 'info',
            self::HIGH => 'warning',
            self::CRITICAL => 'danger',
        };
    }

    // Implementación de HasIcon
    public function getIcon(): ?string
    {
        return match ($this) {
            self::LOW => 'heroicon-m-shield-check',
            self::MEDIUM => 'heroicon-m-minus-circle',
            self::HIGH => 'heroicon-m-exclamation-triangle',
            self::CRITICAL => 'heroicon-m-shield-exclamation',
        };
    }

    /**
     * Clases Tailwind v4 para uso fuera de Filament (Blade, Livewire, etc.)
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::LOW => 'bg-gray-50 text-gray-700 border border-gray-200 dark:bg-gray-500/10 dark:text-gray-400 dark:border-gray-500/20',
            self::MEDIUM => 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
            self::HIGH => 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
            self::CRITICAL => 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20',
        };
    }
}