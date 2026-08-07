<?php

namespace Concept7\LaravelKite\Facades;

use Concept7\LaravelKite\LaravelKite as LaravelKiteInstance;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Concept7\LaravelKite\LaravelKite
 */
class LaravelKite extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LaravelKiteInstance::class;
    }
}
