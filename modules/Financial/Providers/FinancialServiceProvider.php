<?php

namespace Modules\Financial\Providers;

use Modules\Core\Foundation\ModuleServiceProvider;
use Livewire\Livewire;

class FinancialServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'Financial';

    public function register(): void
    {
        parent::register(); 
    }
    
    public function boot(): void
    {
        parent::boot();

        $this->registerLivewireNamespaces();
    }

    protected function registerLivewireNamespaces(): void
    {
        Livewire::addNamespace(
            'financial', 
            classNamespace: 'Modules\\Financial\\App\\Livewire'
        );
    }
}