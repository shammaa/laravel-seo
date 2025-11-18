<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * SEO Meta Tags Facade
 * 
 * @method static \Shammaa\LaravelSEO\Services\MetaTagsManager setTitle(string $title)
 * @method static \Shammaa\LaravelSEO\Services\MetaTagsManager setDescription(string $description)
 * @method static \Shammaa\LaravelSEO\Services\MetaTagsManager setCanonical(string $url)
 * @method static \Shammaa\LaravelSEO\Services\MetaTagsManager addMeta(string $name, string $content, string $type = 'name')
 * @method static \Shammaa\LaravelSEO\Services\MetaTagsManager addAlternateLanguage(string $locale, string $url)
 * @method static string generate()
 * @method static void reset()
 *
 * @see \Shammaa\LaravelSEO\Services\MetaTagsManager
 */
class SEO extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Shammaa\LaravelSEO\Services\MetaTagsManager::class;
    }
}
