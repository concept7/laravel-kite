<?php

use Concept7\LaravelKite\Actions\GetLaravelKiteVersionAction;

return [
    'token' => env('KITE_TOKEN'),

    // Optional: override the Kite API base URL (for development)
    'uri' => env('KITE_URI'),

    // Packages to monitor for EOL alerts. Must be explicitly defined.
    'monitored_packages' => [
        'laravel/framework',
        'statamic/cms',
        'filament/filament',
        'filament/support',
        'livewire/livewire',
        'vite',
        'concept7/laravel-kite',
    ],

    'actions' => [
        GetLaravelKiteVersionAction::class,
    ],
];
