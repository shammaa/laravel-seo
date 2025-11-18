<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO;

use Shammaa\LaravelSEO\Services\SEOService;
use Shammaa\LaravelSEO\Services\MetaTagsManager;
use Shammaa\LaravelSEO\Services\OpenGraphManager;
use Shammaa\LaravelSEO\Services\TwitterCardManager;
use Shammaa\LaravelSEO\Services\JsonLdManager;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

final class LaravelSEOServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/seo.php', 'seo');

        // Register Managers as singletons
        $this->app->singleton(MetaTagsManager::class);
        $this->app->singleton(OpenGraphManager::class);
        $this->app->singleton(TwitterCardManager::class);
        $this->app->singleton(JsonLdManager::class);

        $this->app->singleton(SEOService::class, function (Container $container): SEOService {
            return new SEOService(
                config('seo', []),
                $container->make(MetaTagsManager::class),
                $container->make(OpenGraphManager::class),
                $container->make(TwitterCardManager::class),
                $container->make(JsonLdManager::class)
            );
        });

        $this->app->alias(SEOService::class, 'seo');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/seo.php' => config_path('seo.php'),
        ], 'seo-config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'seo');

        // Register Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Shammaa\LaravelSEO\Console\TestSchemaCommand::class,
                \Shammaa\LaravelSEO\Console\HealthCheckCommand::class,
            ]);
        }

        // Register Facades aliases for backward compatibility
        $this->app->alias(\Shammaa\LaravelSEO\Services\MetaTagsManager::class, 'seotools.meta');
        $this->app->alias(\Shammaa\LaravelSEO\Services\OpenGraphManager::class, 'seotools.opengraph');
        $this->app->alias(\Shammaa\LaravelSEO\Services\TwitterCardManager::class, 'seotools.twitter');
        $this->app->alias(\Shammaa\LaravelSEO\Services\JsonLdManager::class, 'seotools.json-ld');
    }
}

