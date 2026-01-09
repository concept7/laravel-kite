<?php

namespace Concept7\LaravelKite\Actions;

use Closure;
use Composer\InstalledVersions;
use Illuminate\Support\Collection;
use OutOfBoundsException;

class GetFilamentVersionAction
{
    public function handle(Collection $data, Closure $next)
    {
        try {
            $version = InstalledVersions::getVersion('filament/filament');

            if (blank($version)) {
                $version = InstalledVersions::getVersion('filament/support');
            }

            if (filled($version)) {
                $data->push([
                    'key' => 'filament_version',
                    'value' => $version,
                ]);
            }
        } catch (OutOfBoundsException $e) {
        }

        return $next($data);
    }
}
