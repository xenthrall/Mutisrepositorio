<?php

namespace Modules\Academic\Providers;

use Modules\Core\Foundation\Filament\BaseModulePlugin;

class AcademicPlugin extends BaseModulePlugin
{
    protected string $module = 'Academic';

    public function getId(): string
    {
        return 'academic-module';
    }
}