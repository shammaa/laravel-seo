<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Contracts;

/**
 * Contract for providing SEO site settings from database.
 * 
 * Implement this interface in your Settings model to provide
 * site-wide SEO configuration dynamically.
 * 
 * Example in your Model:
 * 
 * class Setting extends Model implements SiteSettingsContract
 * {
 *     public static function getSeoSettings(): array
 *     {
 *         $settings = static::first();
 *         
 *         return [
 *             'name'        => $settings->site_name,
 *             'description' => $settings->site_description,
 *             'logo'        => $settings->logo,
 *         ];
 *     }
 * }
 * 
 * Then in AppServiceProvider:
 * 
 * SEOService::resolveSiteUsing(fn() => Setting::getSeoSettings());
 */
interface SiteSettingsContract
{
    /**
     * Get SEO settings for the site.
     * 
     * @return array{
     *     name: string,
     *     description: string,
     *     logo: string|null,
     *     url?: string,
     *     publisher?: string
     * }
     */
    public static function getSeoSettings(): array;
}
