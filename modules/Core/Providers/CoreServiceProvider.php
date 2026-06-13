<?php

namespace Modules\Core\Providers;

use Modules\Core\Foundation\ModuleServiceProvider;

class CoreServiceProvider extends ModuleServiceProvider
{
    protected string $moduleName = 'Core';

    public function register(): void
    {
        parent::register();

        $this->registerModules();

        $this->optimizes(
            optimize: 'module:cache',
            clear: 'module:clear-cache',
            key: 'erp-modulos',
        );
    }

    public function boot(): void
    {
        parent::boot();

        // Macros
        // Commands
        // Optimize hooks
        // Registries
        // Helpers
    }

    protected function registerModules(): void
    {
        $cachePath = $this->app->bootstrapPath('cache/modules.php');

        if (file_exists($cachePath)) {
            $providers = require $cachePath;
        } else {
            $providers = [];
            $modulesPath = config('modules.path', base_path('modules'));
            if (is_dir($modulesPath)) {
                foreach (scandir($modulesPath) as $moduleName) {
                    if (in_array($moduleName, ['.', '..', 'Core'])) {
                        continue;
                    }
                    $providerClass = "Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider";
                    $providerFile = "{$modulesPath}/{$moduleName}/Providers/{$moduleName}ServiceProvider.php";
                    if (file_exists($providerFile)) {
                        $providers[] = $providerClass;
                    }
                }
            }
        }

        foreach ($providers as $provider) {
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }
}
