<?php

return [
    'monitor_uri' => env('MONITOR_URI'),

    'project_id' => env('MONITOR_CLIENT_PROJECT_ID'),
    'project_key' => env('MONITOR_CLIENT_PROJECT_KEY'),

    'actions' => [
        \Concept7\LaravelKite\Actions\GetPhpVersionAction::class,
        \Concept7\LaravelKite\Actions\GetLaravelVersionAction::class,
        \Concept7\LaravelKite\Actions\GetStatamicVersionAction::class,
        \Concept7\LaravelKite\Actions\GetLivewireVersionAction::class,
        \Concept7\LaravelKite\Actions\GetFilamentVersionAction::class,
        \Concept7\LaravelKite\Actions\GetTailwindVersion::class,
        \Concept7\LaravelKite\Actions\GetViteVersionAction::class,
    ],
];
