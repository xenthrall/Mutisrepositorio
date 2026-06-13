<?php

namespace Modules\Academic\App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StudentGradeEnum: string implements HasLabel, HasColor, HasIcon
{
    // Básica Primaria
    case First = '1';
    case Second = '2';
    case Third = '3';
    case Fourth = '4';
    case Fifth = '5';
    
    // Básica Secundaria
    case Sixth = '6';
    case Seventh = '7';
    case Eighth = '8';
    case Ninth = '9';
    
    // Educación Media
    case Tenth = '10';
    case Eleventh = '11';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::First => 'Primero',
            self::Second => 'Segundo',
            self::Third => 'Tercero',
            self::Fourth => 'Cuarto',
            self::Fifth => 'Quinto',
            self::Sixth => 'Sexto',
            self::Seventh => 'Séptimo',
            self::Eighth => 'Octavo',
            self::Ninth => 'Noveno',
            self::Tenth => 'Décimo',
            self::Eleventh => 'Once',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            // Primaria: Azul
            self::First, self::Second, self::Third, self::Fourth, self::Fifth => 'info',
            // Secundaria: Verde
            self::Sixth, self::Seventh, self::Eighth, self::Ninth => 'success',
            // Media: Naranja/Amarillo
            self::Tenth, self::Eleventh => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            // Primaria
            self::First, self::Second, self::Third, self::Fourth, self::Fifth => 'heroicon-m-pencil',
            // Secundaria
            self::Sixth, self::Seventh, self::Eighth, self::Ninth => 'heroicon-m-book-open',
            // Media
            self::Tenth, self::Eleventh => 'heroicon-m-academic-cap',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::First, self::Second, self::Third, self::Fourth, self::Fifth => 
                'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
            
            self::Sixth, self::Seventh, self::Eighth, self::Ninth => 
                'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
            
            self::Tenth, self::Eleventh => 
                'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
        };
    }
}