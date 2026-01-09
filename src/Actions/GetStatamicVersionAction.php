<?php

namespace Concept7\LaravelKite\Actions;

use Closure;
use Composer\InstalledVersions;
use Illuminate\Support\Collection;
use OutOfBoundsException;

class GetStatamicVersionAction
{
    public function handle(Collection $data, Closure $next)
    {
        try {
            $version = InstalledVersions::getVersion('statamic/cms');

            if (filled($version)) {
                $data->push([
                    'key' => 'statamic_version',
                    'value' => $version,
                ]);
            }
        } catch (OutOfBoundsException $e) {
        }

        return $next($data);
    }
}
