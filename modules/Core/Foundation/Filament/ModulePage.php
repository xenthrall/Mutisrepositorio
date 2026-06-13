<?php

namespace Modules\Core\Foundation\Filament;

use Filament\Pages\Page;
use Modules\Core\Foundation\Filament\Concerns\HasModuleSlug;

/**
 * Clase base para todas las páginas Filament registradas desde módulos.
 *
 * Esta implementación agrega automáticamente un prefijo basado en el nombre
 * del módulo al slug generado por Filament, evitando colisiones de rutas
 * entre módulos diferentes.
 *
 * Ejemplos:
 *
 * Modules\HR\App\Filament\Pages\Dashboard
 * └── dashboard
 *     → hr/dashboard
 *
 * Modules\ER\App\Filament\Pages\Dashboard
 * └── dashboard
 *     → er/dashboard
 *
 * Sin este comportamiento, ambas páginas podrían intentar registrar la misma
 * ruta dentro del panel, provocando conflictos difíciles de detectar a medida
 * que el ERP crece y se agregan nuevos módulos.
 *
 * El prefijo del módulo se obtiene automáticamente a partir del namespace:
 *
 * Modules\HR\...
 * → hr
 *
 * Modules\MercadoLibre\...
 * → mercado-libre
 *
 * Todas las páginas personalizadas de los módulos deberían extender esta clase
 * en lugar de extender directamente {@see \Filament\Pages\Page}.
 */
abstract class ModulePage extends Page
{
    use HasModuleSlug;

    /**
     * Obtiene el slug de la página y le agrega automáticamente el prefijo
     * correspondiente al módulo actual.
     */
    public static function getDefaultSlug(): string
    {
        return static::prependModuleToSlug(
            parent::getDefaultSlug()
        );
    }
}
