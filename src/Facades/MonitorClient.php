<?php

namespace Concept7\MonitorClient\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Concept7\MonitorClient\MonitorClient
 */
class MonitorClient extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Concept7\MonitorClient\MonitorClient::class;
    }
}
