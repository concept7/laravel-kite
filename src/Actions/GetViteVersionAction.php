<?php

namespace Concept7\LaravelKite\Actions;

use Closure;
use Illuminate\Support\Collection;

class GetViteVersionAction
{
    public function handle(Collection $data, Closure $next)
    {
        $packageJsonData = file_get_contents(base_path('package-lock.json'));
        $json = json_decode($packageJsonData, true);

        $version = data_get($json, 'packages.node_modules/vite.version');

        if (filled($version)) {
            $data->push([
                'key' => 'vite_version',
                'value' => $version,
            ]);
        }

        return $next($data);
    }
}
