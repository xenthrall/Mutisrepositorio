<?php

namespace Modules\Core\App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearModuleCacheCommand extends Command
{
    protected $signature = 'module:clear-cache';

    protected $description = 'Elimina el caché de módulos';

    public function handle()
    {
        $cachePath = app()->bootstrapPath('cache/modules.php');

        if (File::exists($cachePath)) {
            File::delete($cachePath);

            $this->info('✅ Caché de módulos eliminado.');

            return self::SUCCESS;
        }

        $this->warn('No existe caché de módulos.');

        return self::SUCCESS;
    }
}