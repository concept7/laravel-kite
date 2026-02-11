<?php

namespace Concept7\LaravelKite\Actions;

use Concept7\Kite\Actions\GetComposerPackageVersionAction;

class GetLivewireVersionAction extends GetComposerPackageVersionAction
{
    public function __construct()
    {
        parent::__construct('livewire_version', ['livewire/livewire']);
    }
}
