<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Shammaa\LaravelSEO\Services\JsonLdManager setTitle(string $title)
 * @method static \Shammaa\LaravelSEO\Services\JsonLdManager setDescription(string $description)
 * @method static \Shammaa\LaravelSEO\Services\JsonLdManager setType(string $type)
 * @method static \Shammaa\LaravelSEO\Services\JsonLdManager addImage(string $url)
 * @method static \Shammaa\LaravelSEO\Services\JsonLdManager add(array $schema)
 * @method static string generate()
 * @method static void reset()
 *
 * @see \Shammaa\LaravelSEO\Services\JsonLdManager
 */
class Schema extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Shammaa\LaravelSEO\Services\JsonLdManager::class;
    }
}

