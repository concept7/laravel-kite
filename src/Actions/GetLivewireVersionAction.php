<?php

namespace Concept7\LaravelKite\Actions;

use Closure;
use Composer\InstalledVersions;
use Illuminate\Support\Collection;
use OutOfBoundsException;

class GetLivewireVersionAction
{
    public function handle(Collection $data, Closure $next)
    {
        try {
            $version = InstalledVersions::getVersion('livewire/livewire');

            if (filled($version)) {
                $data->push([
                    'key' => 'livewire_version',
                    'value' => $version,
                ]);
            }
        } catch (OutOfBoundsException $e) {
        }

        return $next($data);
    }
}
