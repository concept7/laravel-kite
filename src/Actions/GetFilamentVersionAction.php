<?php

namespace Concept7\LaravelKite\Actions;

use Concept7\Kite\Actions\GetComposerPackageVersionAction;

class GetFilamentVersionAction extends GetComposerPackageVersionAction
{
    public function __construct()
    {
        parent::__construct('filament_version', ['filament/filament', 'filament/support']);
    }
}
