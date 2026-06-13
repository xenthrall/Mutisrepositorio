<?php

namespace Modules\Academic\Providers;

use Modules\Core\Foundation\ModuleServiceProvider;
use Livewire\Livewire;

class AcademicServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'Academic';

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
            'academic', 
            classNamespace: 'Modules\\Academic\\App\\Livewire'
        );
    }
}