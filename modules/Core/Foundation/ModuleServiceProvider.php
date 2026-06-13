<?php

namespace Modules\Core\Foundation;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

abstract class ModuleServiceProvider extends ServiceProvider
{
    protected string $moduleName;

    protected function getModulePath(): string
    {
        $basePath = config('modules.path', base_path('modules'));

        return "{$basePath}/{$this->moduleName}";
    }

    public function register(): void
    {
        $modulePath = $this->getModulePath();

        $configPath = $modulePath . '/config/config.php';

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, strtolower($this->moduleName));
        }

        $this->registerCommands($modulePath);
    }

    public function boot(): void
    {
        $modulePath = $this->getModulePath();

        $moduleAlias = strtolower($this->moduleName);

        if (is_dir($path = $modulePath . '/Database/Migrations')) {
            $this->loadMigrationsFrom($path);
        }

        if (is_dir($path = $modulePath . '/resources/views')) {
            $this->loadViewsFrom($path, $moduleAlias);
        }

        if (is_dir($path = $modulePath . '/resources/lang')) {
            $this->loadTranslationsFrom($path, $moduleAlias);
        }

        $this->registerRoutes($modulePath, $moduleAlias);
    }

    protected function registerRoutes(string $modulePath, string $moduleAlias): void
    {
        if (file_exists($path = $modulePath . '/routes/web.php')) {
            Route::middleware('web')
                ->group($path);
        }

        if (file_exists($path = $modulePath . '/routes/api.php')) {
            Route::middleware('api')
                ->prefix("api/{$moduleAlias}")
                ->name("api.{$moduleAlias}.")
                ->group($path);
        }
    }

    protected function registerCommands(string $modulePath): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $commandsPath = $modulePath . '/App/Console';

        if (! is_dir($commandsPath)) {
            return;
        }

        $commands = collect(glob($commandsPath . '/*.php'))
            ->map(function ($file) {
                return sprintf(
                    'Modules\\%s\\App\\Console\\%s',
                    $this->moduleName,
                    basename($file, '.php')
                );
            })
            ->filter(fn ($class) => class_exists($class))
            ->toArray();

        if (! empty($commands)) {
            $this->commands($commands);
        }
    }
}