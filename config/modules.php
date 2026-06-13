<?php
// config/modules.php

return [
    'path' => base_path('modules'),

    'structure' => [
        'base' => [
            'App/Models',
            'App/Services',
            'App/Http/Controllers',
            'Providers',
            'routes',
            'resources/views',
        ],
        'filament' => [
            'App/Filament/Resources',
            'App/Filament/Pages',
            'App/Filament/Widgets',
        ],
        'api' => [
            'App/Http/Resources',
            'App/Http/Requests',
        ],
        'livewire' => [
            'App/Livewire',
            'resources/views/livewire',
        ]
    ],
];