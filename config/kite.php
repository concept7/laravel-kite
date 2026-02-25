<?php

return [
    'uri' => env('KITE_URI'),

    'project_id' => env('KITE_PROJECT_ID'),
    'project_key' => env('KITE_PROJECT_KEY'),

    'actions' => [
        \Concept7\LaravelKite\Actions\GetLaravelKiteVersionAction::class,
        \Concept7\LaravelKite\Actions\GetLaravelVersionAction::class,
        \Concept7\LaravelKite\Actions\GetStatamicVersionAction::class,
        \Concept7\LaravelKite\Actions\GetLivewireVersionAction::class,
        \Concept7\LaravelKite\Actions\GetFilamentVersionAction::class,
        \Concept7\LaravelKite\Actions\GetViteVersionAction::class,
    ],
];
