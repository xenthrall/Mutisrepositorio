<?php

namespace Modules\Core\App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CacheModulesCommand extends Command
{
    protected $signature = 'module:cache';

    protected $description = 'Descubre y cachea los Service Providers de los módulos';

    protected array $excludedModules = [
        'Core',
    ];

    public function handle()
    {
        $this->info('Escaneando módulos...');

        $modulesPath = config('modules.path', base_path('modules'));
        $cachePath = app()->bootstrapPath('cache/modules.php');

        $providers = [];

        if (! File::exists($modulesPath)) {
            $this->error("El directorio de módulos no existe: {$modulesPath}");
            return self::FAILURE;
        }

        foreach (File::directories($modulesPath) as $module) {

            $moduleName = basename($module);

            if (in_array($moduleName, $this->excludedModules)) {
                continue;
            }

            $providerClass = "Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider";

            $providerPath = "{$module}/Providers/{$moduleName}ServiceProvider.php";

            if (File::exists($providerPath)) {
                $providers[] = $providerClass;
            }
        }

        $content = "<?php\n\nreturn [\n";

        foreach ($providers as $provider) {
            $content .= "    '{$provider}',\n";
        }

        $content .= "];\n";

        File::put($cachePath, $content);

        $this->info("✅ Se han cacheado " . count($providers) . " módulos correctamente.");

        return self::SUCCESS;
    }
}
