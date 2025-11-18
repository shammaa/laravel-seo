<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Shammaa\LaravelSEO\Services\TwitterCardManager setType(string $type)
 * @method static \Shammaa\LaravelSEO\Services\TwitterCardManager setTitle(string $title)
 * @method static \Shammaa\LaravelSEO\Services\TwitterCardManager setDescription(string $description)
 * @method static \Shammaa\LaravelSEO\Services\TwitterCardManager setImage(string $url)
 * @method static \Shammaa\LaravelSEO\Services\TwitterCardManager addValue(string $key, string $value)
 * @method static string generate()
 * @method static void reset()
 *
 * @see \Shammaa\LaravelSEO\Services\TwitterCardManager
 */
class Twitter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Shammaa\LaravelSEO\Services\TwitterCardManager::class;
    }
}

