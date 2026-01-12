<?php

namespace Concept7\LaravelKite\Actions;

use Illuminate\Support\Facades\Process;

class GetProjectInformationAction
{
    public function handle()
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

    public function getComposerPackageDetail()
    {
        $result = Process::run(config('kite.php_path').' vendor/bin/composer show -D --format=json --no-dev');
        $data = json_decode($result->output());

        if (blank($data)) {
            return [];
        }

        return $data->installed;
    }
}
