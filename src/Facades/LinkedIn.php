<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * LinkedIn uses OpenGraph tags, so this facade provides access to OpenGraphManager
 * for LinkedIn-specific meta tags.
 *
 * @method static \Shammaa\LaravelSEO\Services\OpenGraphManager setTitle(string $title)
 * @method static \Shammaa\LaravelSEO\Services\OpenGraphManager setDescription(string $description)
 * @method static \Shammaa\LaravelSEO\Services\OpenGraphManager setUrl(string $url)
 * @method static \Shammaa\LaravelSEO\Services\OpenGraphManager setType(string $type)
 * @method static \Shammaa\LaravelSEO\Services\OpenGraphManager addProperty(string $property, string $value)
 * @method static \Shammaa\LaravelSEO\Services\OpenGraphManager addImage(string $url, ?int $width = null, ?int $height = null, ?string $type = null, ?string $alt = null)
 * @method static string generate()
 * @method static void reset()
 *
 * @see \Shammaa\LaravelSEO\Services\OpenGraphManager
 */
class LinkedIn extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Shammaa\LaravelSEO\Services\OpenGraphManager::class;
    }
}

