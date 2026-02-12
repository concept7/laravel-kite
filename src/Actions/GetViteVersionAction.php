<?php

namespace Concept7\LaravelKite\Actions;

use Concept7\Kite\Actions\GetNodePackageVersionAction;

class GetViteVersionAction extends GetNodePackageVersionAction
{
    public function __construct()
    {
        parent::__construct('vite_version', 'vite');
    }
}
