<?php

return [
    'monitor_uri' => env('MONITOR_URI', 'https://monitor.test'),

    'project_id' => env('MONITOR_CLIENT_PROJECT_ID'),
    'project_key' => env('MONITOR_CLIENT_PROJECT_KEY'),

    'actions' => [
        \Concept7\MonitorClient\Actions\GetPhpVersionAction::class,
        \Concept7\MonitorClient\Actions\GetLaravelVersionAction::class,
        \Concept7\MonitorClient\Actions\GetStatamicVersionAction::class,
        \Concept7\MonitorClient\Actions\GetLivewireVersionAction::class,
        \Concept7\MonitorClient\Actions\GetTailwindVersion::class,
        \Concept7\MonitorClient\Actions\GetViteVersionAction::class,
    ],
];
