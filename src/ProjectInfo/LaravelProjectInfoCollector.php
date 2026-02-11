<?php

namespace Concept7\LaravelKite\ProjectInfo;

use Concept7\Kite\Contracts\ProjectInfoCollectorInterface;
use Illuminate\Support\Facades\Process;

class LaravelProjectInfoCollector implements ProjectInfoCollectorInterface
{
    public function __construct(
        protected string $phpPath = 'php',
    ) {}

    public function collect(): array
    {
        $app = app();

        return [
            'hostname' => gethostname(),
            'is_debug_mode_on' => $app->hasDebugModeEnabled(),
            'environment' => $app->environment(),
            'laravel_version' => $app->version(),
            'is_maintenance_mode_on' => $app->isDownForMaintenance(),
            'php_version' => phpversion(),
            'url' => config('app.url'),
            'packages' => $this->getComposerPackageDetail(),
        ];
    }

    private function getComposerPackageDetail(): array
    {
        $result = Process::run($this->phpPath.' vendor/bin/composer show -D --format=json --no-dev');
        $data = json_decode($result->output());

        if (blank($data)) {
            return [];
        }

        return $data->installed;
    }
}
