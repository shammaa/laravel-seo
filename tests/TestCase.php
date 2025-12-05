<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Shammaa\LaravelSEO\LaravelSEOServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Setup config for testing
        $this->app['config']->set('cache.default', 'array');
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelSEOServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function defineEnvironment($app): void
    {
        // Setup cache
        $app['config']->set('cache.default', 'array');

        // Setup SEO config
        $app['config']->set('seo', [
            'site' => [
                'name' => 'Test Site',
                'description' => 'Test Description',
                'url' => 'http://localhost',
                'locale' => 'en',
            ],
        ]);
    }
}

