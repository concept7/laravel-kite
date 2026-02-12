<?php

namespace Concept7\LaravelKite\Actions;

use Concept7\Kite\Actions\GetNodePackageVersionAction;

class GetViteVersionAction extends GetNodePackageVersionAction
{
    public function __construct(string $projectRoot)
    {
        parent::__construct($projectRoot, 'vite_version', 'vite');
    }
}
