<?php

namespace Concept7\MonitorClient\Actions;

use Closure;
use Illuminate\Support\Collection;

class GetViteVersionAction
{
    public function handle(Collection $data, Closure $next)
    {
        $packageJsonData = file_get_contents('package-lock.json');
        $json = json_decode($packageJsonData, true);

        $version = data_get($json, 'packages.node_modules/vite.version');

        $data->push([
            'key' => 'vite_version',
            'value' => $version,
        ]);

        return $next($data);
    }
}
