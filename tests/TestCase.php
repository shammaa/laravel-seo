<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Shammaa\LaravelSEO\LaravelSEOServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelSEOServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app) {}
}
