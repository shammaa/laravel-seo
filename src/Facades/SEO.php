<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * SEO Service Facade - Main facade for SEO operations
 * 
 * @method static \Shammaa\LaravelSEO\Services\SEOService for(string $pageType, $model = null)
 * @method static \Shammaa\LaravelSEO\Services\SEOService home()
 * @method static \Shammaa\LaravelSEO\Services\SEOService post($model)
 * @method static \Shammaa\LaravelSEO\Services\SEOService category($model)
 * @method static \Shammaa\LaravelSEO\Services\SEOService product($model)
 * @method static \Shammaa\LaravelSEO\Services\SEOService search(array $params)
 * @method static string render()
 * @method static void set()
 * @method static array breadcrumb()
 * @method static \Shammaa\LaravelSEO\Services\SEOService addProduct($model)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addAggregateRating(float $ratingValue, int $ratingCount, float $bestRating = 5.0, float $worstRating = 1.0)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addBrand(string $name, ?string $logo = null, ?string $url = null)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addFAQ(array $faqs)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addHowTo(string $name, array $steps, ?string $description = null, ?string $image = null)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addReview(string $itemName, float $ratingValue, float $bestRating = 5.0, ?string $reviewBody = null, ?string $authorName = null, ?string $datePublished = null)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addEvent(string $name, string $startDate, ?string $endDate = null, ?string $description = null, ?string $locationName = null, ?string $locationAddress = null, ?string $image = null, ?string $organizerName = null, ?string $organizerUrl = null)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addCourse(array $data)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addRecipe(array $data)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addJobPosting(array $data)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addLocalBusiness(array $data)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addSoftwareApplication(array $data)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addBook(array $data)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addMovie(array $data)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addPodcast(array $data)
 * @method static \Shammaa\LaravelSEO\Services\SEOService addVideo(array $data)
 *
 * @see \Shammaa\LaravelSEO\Services\SEOService
 */
class SEO extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'seo';
    }
}
