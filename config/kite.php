<?php

return [
    'token' => env('KITE_TOKEN'),

    // Optional: override the Kite API base URL (for development)
    'uri' => env('KITE_URI'),

    'actions' => [
        \Concept7\LaravelKite\Actions\GetLaravelKiteVersionAction::class,
        \Concept7\LaravelKite\Actions\GetLaravelVersionAction::class,
        \Concept7\LaravelKite\Actions\GetStatamicVersionAction::class,
        \Concept7\LaravelKite\Actions\GetLivewireVersionAction::class,
        \Concept7\LaravelKite\Actions\GetFilamentVersionAction::class,
        \Concept7\LaravelKite\Actions\GetViteVersionAction::class,
    ],
];
