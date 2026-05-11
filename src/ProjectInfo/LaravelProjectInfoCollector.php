<?php

namespace Concept7\LaravelKite\ProjectInfo;

use Concept7\Kite\Contracts\ProjectInfoCollectorInterface;
use Concept7\Kite\Support\ComposerDependencies;
use Concept7\Kite\Support\NpmDependencies;

class LaravelProjectInfoCollector implements ProjectInfoCollectorInterface
{
    public function collect(): array
    {
        $app = app();

        $packages = array_merge(
            ComposerDependencies::all(),
            NpmDependencies::installed(),
        );

        return [
            'hostname' => gethostname(),
            'is_debug_mode_on' => $app->hasDebugModeEnabled(),
            'environment' => $app->environment(),
            'laravel_version' => $app->version(),
            'is_maintenance_mode_on' => $app->isDownForMaintenance(),
            'php_version' => phpversion(),
            'url' => config('app.url'),
            'packages' => $packages,
            'monitored_packages' => $this->resolveMonitoredPackages(),
        ];
    }

    private function resolveMonitoredPackages(): array
    {
        return config('kite.monitored_packages', []);
    }
}
