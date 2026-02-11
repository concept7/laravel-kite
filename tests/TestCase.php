<?php

namespace Concept7\LaravelKite\Tests;

use Concept7\LaravelKite\LaravelKiteServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelKiteServiceProvider::class,
        ];
    }
}
