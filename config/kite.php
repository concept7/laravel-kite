<?php

use Concept7\LaravelKite\Actions\GetFilamentVersionAction;
use Concept7\LaravelKite\Actions\GetLaravelKiteVersionAction;
use Concept7\LaravelKite\Actions\GetLaravelVersionAction;
use Concept7\LaravelKite\Actions\GetLivewireVersionAction;
use Concept7\LaravelKite\Actions\GetStatamicVersionAction;
use Concept7\LaravelKite\Actions\GetViteVersionAction;

return [
    'token' => env('KITE_TOKEN'),

    // Optional: override the Kite API base URL (for development)
    'uri' => env('KITE_URI'),

    'actions' => [
        GetLaravelKiteVersionAction::class,
        GetLaravelVersionAction::class,
        GetStatamicVersionAction::class,
        GetLivewireVersionAction::class,
        GetFilamentVersionAction::class,
        GetViteVersionAction::class,
    ],
];
