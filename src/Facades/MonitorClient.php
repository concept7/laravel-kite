<?php

namespace Concept7\LaravelKite\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Concept7\LaravelKite\LaravelKite
 */
class MonitorClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Concept7\LaravelKite\LaravelKite::class;
    }
}
