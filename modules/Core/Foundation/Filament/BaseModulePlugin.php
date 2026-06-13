<?php

namespace Modules\Core\Foundation\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;

abstract class BaseModulePlugin implements Plugin
{
    protected string $module;

    public function register(Panel $panel): void
    {
        $modulesPath = config('modules.path', base_path('modules'));
        
        $basePath = "{$modulesPath}/{$this->module}/App/Filament";
        
        $namespace = "Modules\\{$this->module}\\App\\Filament";

        $panel
            ->discoverResources(
                in: "{$basePath}/Resources",
                for: "{$namespace}\\Resources"
            )
            ->discoverPages(
                in: "{$basePath}/Pages",
                for: "{$namespace}\\Pages"
            );
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}