<?php

namespace Modules\Academic\App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class AcademicDashboard extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    
    protected static string|BackedEnum|null $navigationIcon = Heroicon::PuzzlePiece;

    // Agrupa la página bajo el nombre del módulo
    protected static string|\UnitEnum|null $navigationGroup = 'Academic';

    // Usamos el alias del módulo (en minúsculas) para la vista
    protected string $view = 'academic::filament.pages.dashboard';

    public function getTitle(): string
    {
        return 'Bienvenido a Academic';
    }
}