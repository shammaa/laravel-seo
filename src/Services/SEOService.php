<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Services;

use Shammaa\LaravelSEO\Builders\MetaTagsBuilder;
use Shammaa\LaravelSEO\Builders\OpenGraphBuilder;
use Shammaa\LaravelSEO\Builders\TwitterCardBuilder;
use Shammaa\LaravelSEO\Builders\LinkedInCardBuilder;
use Shammaa\LaravelSEO\Builders\SchemaBuilder;
use Shammaa\LaravelSEO\Builders\MultilingualBuilder;
use Shammaa\LaravelSEO\Builders\PerformanceBuilder;
use Shammaa\LaravelSEO\Builders\MobileBuilder;
use Shammaa\LaravelSEO\Builders\SecurityBuilder;
use Shammaa\LaravelSEO\Builders\AnalyticsBuilder;
use Shammaa\LaravelSEO\Schemas\BreadcrumbSchema;
use Shammaa\LaravelSEO\Data\PageData;
use Illuminate\Support\Str;

final class SEOService
{
    private ?string $pageType = null;
    private $model = null;
    private array $config = [];
    private ?PageData $pageData = null;

    public function __construct(
        array $config = [],
        private MetaTagsManager $metaTagsManager,
        private OpenGraphManager $openGraphManager,
        private TwitterCardManager $twitterCardManager,
        private JsonLdManager $jsonLdManager
    ) {
        $this->config = $config;
    }

    public function for(string $pageType, $model = null): self
    {
        $this->pageType = $pageType;
        $this->model = $model;
        $this->pageData = null;

        return $this;
    }

    public function home(): self
    {
        return $this->for('home');
    }

    public function post($model): self
    {
        return $this->for('post', $model);
    }

    public function category($model): self
    {
        return $this->for('category', $model);
    }

    public function search(array $params): self
    {
        return $this->for('search', $params);
    }

    public function product($model): self
    {
        return $this->for('product', $model);
    }

    public function set(): void
    {
        // Reset all managers to avoid duplicates
        $this->metaTagsManager->reset();
        $this->openGraphManager->reset();
        $this->twitterCardManager->reset();
        $this->jsonLdManager->reset();
        
        $pageData = $this->getPageData();
        $siteData = $this->getSiteData();

        // Build Meta Tags
        (new MetaTagsBuilder($this->config, $this->metaTagsManager))->build($pageData, $this->pageType, $this->model);

        // Build OpenGraph (Facebook)
        (new OpenGraphBuilder($this->config, $this->openGraphManager))->build($pageData, $this->pageType, $siteData);

        // Build Twitter Card
        (new TwitterCardBuilder($this->config, $this->twitterCardManager))->build($pageData, $this->model);

        // Build LinkedIn Card
        (new LinkedInCardBuilder($this->config, $this->openGraphManager))->build($pageData);
        
        // Build Multilingual (Hreflang)
        (new MultilingualBuilder($this->config, $this->metaTagsManager))->build($this->model);
        
        // Build Mobile Tags
        (new MobileBuilder($this->config, $this->metaTagsManager))->build();
        
        // Build Security Headers
        (new SecurityBuilder($this->config, $this->metaTagsManager))->build();

        // Build Geo-targeting
        (new \Shammaa\LaravelSEO\Builders\GeoBuilder($this->config, $this->metaTagsManager))->build();

        // Build Social Media (Pinterest, WhatsApp, Telegram)
        (new \Shammaa\LaravelSEO\Builders\SocialMediaBuilder($this->config, $this->metaTagsManager, $this->openGraphManager))->build();

        // Auto-detect and add schemas from model if using HasSEO trait
        $this->autoDetectSchemas();

        // Build Basic JsonLd (for non-article and non-product pages)
        if ($pageData->schema !== 'NewsArticle' && $pageData->schema !== 'Product') {
            $this->buildBasicJsonLd($pageData);
        }

        // Build Custom Schemas
        $schemas = (new SchemaBuilder($this->config))->build(
            $this->pageType,
            $pageData,
            $this->model,
            $siteData
        );

        view()->share('customSchemas', $schemas);
        
        // Share breadcrumb items for HTML rendering
        if (in_array($this->pageType, ['post', 'category', 'product', 'tag', 'author', 'archive', 'page'])) {
            $breadcrumbSchema = new BreadcrumbSchema($this->config);
            $breadcrumbs = $breadcrumbSchema->getItems($this->model, $siteData, $this->pageType);
            view()->share('breadcrumbs', $breadcrumbs);
        }
        
        // Share performance optimization tags
        $performanceHtml = (new PerformanceBuilder($this->config))->build();
        view()->share('performanceTags', $performanceHtml);
        
        // Share analytics tags
        $analyticsHtml = (new AnalyticsBuilder($this->config))->build();
        view()->share('analyticsTags', $analyticsHtml);
        
        // Share AMP link if enabled
        if (!empty($this->config['amp']['enabled']) && $this->pageType === 'post' && $this->model) {
            $ampUrl = $this->getAmpUrl($this->model);
            if ($ampUrl) {
                view()->share('ampUrl', $ampUrl);
            }
        }
        
        // Share pagination links if available
        if (!empty($this->config['pagination']['enabled']) && $this->model) {
            $pagination = $this->getPaginationLinks($this->model);
            if ($pagination) {
                view()->share('paginationLinks', $pagination);
            }
        }
    }

    public function render(): string
    {
        $this->set();
        return view('seo::meta-tags')->render();
    }

    /**
     * Get breadcrumb items for HTML rendering
     */
    public function breadcrumb(): array
    {
        if ($this->pageType === null) {
            return [];
        }

        $siteData = $this->getSiteData();
        $breadcrumbSchema = new BreadcrumbSchema($this->config);
        
        return $breadcrumbSchema->getItems($this->model, $siteData, $this->pageType);
    }

    /**
     * Add Product Schema
     */
    public function addProduct($model): self
    {
        $pageData = $this->getPageData();
        $siteData = $this->getSiteData();
        
        $productSchema = new \Shammaa\LaravelSEO\Schemas\ProductSchema($this->config);
        $schema = $productSchema->build($pageData, $model, $siteData);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add AggregateRating Schema
     */
    public function addAggregateRating(
        float $ratingValue,
        int $ratingCount,
        float $bestRating = 5.0,
        float $worstRating = 1.0
    ): self {
        $aggregateRatingSchema = new \Shammaa\LaravelSEO\Schemas\AggregateRatingSchema($this->config);
        $schema = $aggregateRatingSchema->build($ratingValue, $ratingCount, $bestRating, $worstRating);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add Brand Schema
     */
    public function addBrand(string $name, ?string $logo = null, ?string $url = null): self
    {
        $brandSchema = new \Shammaa\LaravelSEO\Schemas\BrandSchema($this->config);
        $schema = $brandSchema->build($name, $logo, $url);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add FAQ Schema
     */
    public function addFAQ(array $faqs): self
    {
        $faqSchema = new \Shammaa\LaravelSEO\Schemas\FAQSchema($this->config);
        $schema = $faqSchema->build($faqs);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add HowTo Schema
     */
    public function addHowTo(string $name, array $steps, ?string $description = null, ?string $image = null): self
    {
        $howToSchema = new \Shammaa\LaravelSEO\Schemas\HowToSchema($this->config);
        $schema = $howToSchema->build($name, $steps, $description, $image);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add Review Schema
     */
    public function addReview(
        string $itemName,
        float $ratingValue,
        float $bestRating = 5.0,
        ?string $reviewBody = null,
        ?string $authorName = null,
        ?string $datePublished = null
    ): self {
        $reviewSchema = new \Shammaa\LaravelSEO\Schemas\ReviewSchema($this->config);
        $schema = $reviewSchema->build($itemName, $ratingValue, $bestRating, $reviewBody, $authorName, $datePublished);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add Event Schema
     */
    public function addEvent(
        string $name,
        string $startDate,
        ?string $endDate = null,
        ?string $description = null,
        ?string $locationName = null,
        ?string $locationAddress = null,
        ?string $image = null,
        ?string $organizerName = null,
        ?string $organizerUrl = null
    ): self {
        $eventSchema = new \Shammaa\LaravelSEO\Schemas\EventSchema($this->config);
        $schema = $eventSchema->build($name, $startDate, $endDate, $description, $locationName, $locationAddress, $image, $organizerName, $organizerUrl);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add Course Schema
     */
    public function addCourse(array $data): self
    {
        $courseSchema = new \Shammaa\LaravelSEO\Schemas\CourseSchema($this->config);
        $schema = $courseSchema->build($data);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add Recipe Schema
     */
    public function addRecipe(array $data): self
    {
        $recipeSchema = new \Shammaa\LaravelSEO\Schemas\RecipeSchema($this->config);
        $schema = $recipeSchema->build($data);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add JobPosting Schema
     */
    public function addJobPosting(array $data): self
    {
        $jobPostingSchema = new \Shammaa\LaravelSEO\Schemas\JobPostingSchema($this->config);
        $schema = $jobPostingSchema->build($data);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add LocalBusiness Schema
     */
    public function addLocalBusiness(array $data): self
    {
        $localBusinessSchema = new \Shammaa\LaravelSEO\Schemas\LocalBusinessSchema($this->config);
        $schema = $localBusinessSchema->build($data);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add SoftwareApplication Schema
     */
    public function addSoftwareApplication(array $data): self
    {
        $softwareSchema = new \Shammaa\LaravelSEO\Schemas\SoftwareApplicationSchema($this->config);
        $schema = $softwareSchema->build($data);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add Book Schema
     */
    public function addBook(array $data): self
    {
        $bookSchema = new \Shammaa\LaravelSEO\Schemas\BookSchema($this->config);
        $schema = $bookSchema->build($data);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add Movie Schema
     */
    public function addMovie(array $data): self
    {
        $movieSchema = new \Shammaa\LaravelSEO\Schemas\MovieSchema($this->config);
        $schema = $movieSchema->build($data);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add Podcast Schema
     */
    public function addPodcast(array $data): self
    {
        $podcastSchema = new \Shammaa\LaravelSEO\Schemas\PodcastSchema($this->config);
        $schema = $podcastSchema->build($data);
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    /**
     * Add Video Schema (Enhanced)
     */
    public function addVideo(array $data): self
    {
        $videoSchema = new \Shammaa\LaravelSEO\Schemas\VideoSchema($this->config);
        $pageData = $this->getPageData();
        $siteData = $this->getSiteData();
        
        // Create a model-like object from data
        $model = (object) $data;
        if (isset($data['video_url'])) {
            $model->video_url = $data['video_url'];
        }
        if (isset($data['image'])) {
            $pageData->image = $data['image'];
        }
        
        $schema = $videoSchema->build($pageData, $model, $siteData);
        
        // Enhance with additional data
        if (isset($data['duration'])) {
            $schema['duration'] = $data['duration'];
        }
        if (isset($data['contentUrl'])) {
            $schema['contentUrl'] = $data['contentUrl'];
        }
        if (isset($data['interactionStatistic'])) {
            $schema['interactionStatistic'] = $data['interactionStatistic'];
        }
        
        if (!empty($schema)) {
            $customSchemas = view()->getShared()['customSchemas'] ?? '';
            $schemas = $this->parseSchemas($customSchemas);
            $schemas[] = $schema;
            view()->share('customSchemas', $this->renderSchemas($schemas));
        }
        
        return $this;
    }

    private function parseSchemas(string $html): array
    {
        $schemas = [];
        preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/s', $html, $matches);
        
        foreach ($matches[1] ?? [] as $json) {
            $schema = json_decode(trim($json), true);
            if ($schema) {
                $schemas[] = $schema;
            }
        }
        
        return $schemas;
    }

    private function renderSchemas(array $schemas): string
    {
        $html = '';
        foreach ($schemas as $schema) {
            if (empty($schema)) {
                continue;
            }
            
            $html .= '<script type="application/ld+json">';
            $html .= json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $html .= '</script>' . PHP_EOL;
        }
        return $html;
    }

    private function getPageData(): PageData
    {
        if ($this->pageData !== null) {
            return $this->pageData;
        }

        $siteData = $this->getSiteData();
        $dataExtractor = new PageDataExtractor($this->config);

        $this->pageData = $dataExtractor->extract($this->pageType, $this->model, $siteData);

        return $this->pageData;
    }

    private function getSiteData(): array
    {
        $locale = $this->config['site']['locale'] ?? app()->getLocale();
        $cacheKey = "seo_site_data_{$locale}";
        $cacheTtl = $this->config['cache_ttl'] ?? 86400;

        return cache()->remember($cacheKey, $cacheTtl, function () use ($locale) {
            $siteConfig = $this->config['site'] ?? [];

            return [
                'name' => $siteConfig['name'] ?? config('app.name'),
                'description' => $siteConfig['description'] ?? '',
                'logo' => $this->getLogoUrl($siteConfig['logo'] ?? null),
                'url' => $siteConfig['url'] ?? url('/'),
                'locale' => $locale,
            ];
        });
    }

    private function getLogoUrl(?string $logoPath): string
    {
        if (empty($logoPath)) {
            return asset($this->config['defaults']['logo'] ?? 'images/default-logo.jpg');
        }

        if (filter_var($logoPath, FILTER_VALIDATE_URL)) {
            return $logoPath;
        }

        // Support for image route helper
        if (function_exists('route') && $this->config['image_route'] ?? null) {
            $routeName = $this->config['image_route']['name'] ?? 'image';
            $size = $this->config['image_route']['logo_size'] ?? '265x85';
            
            try {
                return route($routeName, [
                    'size' => $size,
                    'path' => $logoPath
                ]);
            } catch (\Exception $e) {
                // Fallback to asset
            }
        }

        return asset($logoPath);
    }

    private function buildBasicJsonLd(PageData $pageData): void
    {
        $imageUrl = $this->normalizeImageUrl($pageData->image);
        $siteData = $this->getSiteData();

        // Build WebPage schema - use add() directly to avoid duplication
        $this->jsonLdManager->add([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'headline' => $pageData->title,
            'name' => $pageData->title,
            'description' => $pageData->description,
            'url' => request()->url(),
            'image' => $imageUrl,
            'inLanguage' => $siteData['locale'] ?? app()->getLocale(),
        ]);
    }

    private function normalizeImageUrl(?string $imagePath): string
    {
        if (empty($imagePath)) {
            return asset($this->config['defaults']['image'] ?? 'images/default.jpg');
        }

        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Extract filename if it's a full URL path
        if (strpos($imagePath, '/') !== false) {
            $imagePath = basename($imagePath);
        }

        // Support for image route helper
        if (function_exists('route') && $this->config['image_route'] ?? null) {
            $routeName = $this->config['image_route']['name'] ?? 'image';
            $size = $this->config['image_route']['og_size'] ?? '1200x630';
            
            try {
                return route($routeName, [
                    'size' => $size,
                    'path' => $imagePath
                ]);
            } catch (\Exception $e) {
                // Fallback to asset
            }
        }

        return asset($imagePath);
    }
    
    private function getAmpUrl($model): ?string
    {
        $ampConfig = $this->config['amp'] ?? [];
        
        if (empty($ampConfig['enabled']) || empty($ampConfig['url_generator'])) {
            return null;
        }
        
        if (is_callable($ampConfig['url_generator'])) {
            return $ampConfig['url_generator']($model);
        }
        
        // Default: add /amp/ prefix
        if (is_object($model) && method_exists($model, 'route')) {
            try {
                $baseUrl = $model->route();
                return str_replace('/posts/', '/amp/posts/', $baseUrl);
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return null;
    }
    
    private function getPaginationLinks($model): ?array
    {
        $paginationConfig = $this->config['pagination'] ?? [];
        
        if (empty($paginationConfig['enabled'])) {
            return null;
        }
        
        $links = [];
        
        // Previous link
        if (is_object($model) && isset($model->previous) && $model->previous) {
            if (method_exists($model->previous, 'route')) {
                try {
                    $links['prev'] = $model->previous->route();
                } catch (\Exception $e) {
                    // Skip
                }
            }
        }
        
        // Next link
        if (is_object($model) && isset($model->next) && $model->next) {
            if (method_exists($model->next, 'route')) {
                try {
                    $links['next'] = $model->next->route();
                } catch (\Exception $e) {
                    // Skip
                }
            }
        }
        
        return !empty($links) ? $links : null;
    }

    /**
     * Auto-detect and add schemas from model if using HasSEO trait
     */
    private function autoDetectSchemas(): void
    {
        if (!$this->model || !is_object($this->model)) {
            return;
        }

        $traitName = 'Shammaa\\LaravelSEO\\Traits\\HasSEO';
        
        // Check if model uses HasSEO trait
        if (!in_array($traitName, class_uses_recursive($this->model))) {
            return;
        }

        // Auto-load required relationships
        $this->autoLoadSEORelationships();

        // Auto-add FAQ Schema
        if (method_exists($this->model, 'hasSEOFAQs') && $this->model->hasSEOFAQs()) {
            $faqs = $this->model->getSEOFAQs();
            if (!empty($faqs)) {
                $this->addFAQ($faqs);
            }
        }

        // Auto-add HowTo Schema
        if (method_exists($this->model, 'hasSEOHowToSteps') && $this->model->hasSEOHowToSteps()) {
            $steps = $this->model->getSEOHowToSteps();
            if (!empty($steps)) {
                $this->addHowTo(
                    name: $this->model->getSEOTitle() ?? 'How To',
                    steps: $steps,
                    description: $this->model->getSEODescription(),
                    image: $this->model->getSEOImage()
                );
            }
        }

        // Auto-add Review Schema
        if (method_exists($this->model, 'hasSEOReview') && $this->model->hasSEOReview()) {
            $review = $this->model->getSEOReview();
            if ($review && !empty($review['itemName']) && !empty($review['ratingValue'])) {
                $this->addReview(
                    itemName: $review['itemName'],
                    ratingValue: $review['ratingValue'],
                    bestRating: $review['bestRating'] ?? 5.0,
                    reviewBody: $review['reviewBody'] ?? null,
                    authorName: $review['authorName'] ?? null,
                    datePublished: $review['datePublished'] ?? null
                );
            }
        }

        // Auto-add Event Schema
        if (method_exists($this->model, 'hasSEOEvent') && $this->model->hasSEOEvent()) {
            $event = $this->model->getSEOEvent();
            if ($event && !empty($event['name']) && !empty($event['startDate'])) {
                $this->addEvent(
                    name: $event['name'],
                    startDate: $event['startDate'],
                    endDate: $event['endDate'] ?? null,
                    description: $event['description'] ?? null,
                    locationName: $event['locationName'] ?? null,
                    locationAddress: $event['locationAddress'] ?? null,
                    image: $event['image'] ?? null,
                    organizerName: $event['organizerName'] ?? null,
                    organizerUrl: $event['organizerUrl'] ?? null
                );
            }
        }
    }

    /**
     * Auto-load SEO relationships from model
     */
    private function autoLoadSEORelationships(): void
    {
        if (!method_exists($this->model, 'getSEORelationshipsToLoad')) {
            return;
        }

        $relationships = $this->model->getSEORelationshipsToLoad();
        
        if (empty($relationships)) {
            return;
        }

        // Use loadMissing to avoid loading already loaded relationships
        // This prevents N+1 queries and is efficient
        try {
            $this->model->loadMissing($relationships);
        } catch (\Exception $e) {
            // Silently fail if relationship doesn't exist
            // This allows models to define relationships conditionally
        }
    }
}

