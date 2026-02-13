<?php

namespace Concept7\LaravelKite\Actions;

use Concept7\Kite\Actions\GetComposerPackageVersionAction;

class GetLaravelKiteVersionAction extends GetComposerPackageVersionAction
{
    public function __construct()
    {
        parent::__construct('laravel_kite_version', ['concept7/laravel-kite']);
    }
}
