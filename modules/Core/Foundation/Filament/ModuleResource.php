<?php

namespace Modules\Core\Foundation\Filament;

use Filament\Resources\Resource;
use Modules\Core\Foundation\Filament\Concerns\HasModuleSlug;

/**
 * Clase base para todos los Resources modulares de la aplicación.
 *
 * Extiende el comportamiento estándar de Filament agregando aislamiento
 * automático de slugs por módulo.
 *
 * Beneficios:
 *
 * - Evita colisiones de rutas entre módulos.
 * - Mantiene una estructura consistente de URLs.
 * - Elimina la necesidad de definir manualmente $slug en cada Resource.
 * - Centraliza la lógica de generación de slugs en un único punto.
 *
 * Ejemplos:
 *
 * Modules\HR\App\Filament\Resources\Employees\EmployeeResource
 * -> hr/employees
 *
 * Modules\Warranties\App\Filament\Resources\WarrantyRequests\WarrantyRequestResource
 * -> warranties/warranty-requests
 *
 * Modules\MercadoLibre\App\Filament\Resources\MlPublications\MlPublicationResource
 * -> mercado-libre/ml-publications
 *
 * Todos los Resources de módulos deberían extender esta clase en lugar de
 * Filament\Resources\Resource directamente.
 */
abstract class ModuleResource extends Resource
{
    use HasModuleSlug;

    /**
     * Genera el slug base del Resource incorporando automáticamente
     * el prefijo correspondiente al módulo.
     *
     * @return string
     */
    public static function getDefaultSlug(): string
    {
        return static::prependModuleToSlug(
            parent::getDefaultSlug()
        );
    }
}