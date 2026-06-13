<?php

namespace Modules\Financial\Providers;

use Modules\Core\Foundation\Filament\BaseModulePlugin;

class FinancialPlugin extends BaseModulePlugin
{
    protected string $module = 'Financial';

    public function getId(): string
    {
        return 'financial-module';
    }
}