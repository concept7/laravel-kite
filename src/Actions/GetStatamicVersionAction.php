<?php

namespace Concept7\LaravelKite\Actions;

use Concept7\Kite\Actions\GetComposerPackageVersionAction;

class GetStatamicVersionAction extends GetComposerPackageVersionAction
{
    public function __construct()
    {
        parent::__construct('statamic_version', ['statamic/cms']);
    }
}
