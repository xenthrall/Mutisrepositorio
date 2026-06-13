<?php

namespace Modules\Core\App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Modules\Core\App\Enums\PermissionCriticality;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'module',
        'domain',
        'criticality',
        'is_system',
        'group',
        'description',
        'action',
    ];

    protected $casts = [
        'criticality' => PermissionCriticality::class,
        'is_system' => 'boolean',
    ];
}

