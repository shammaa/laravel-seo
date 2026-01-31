<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Data\PageData;
use Shammaa\LaravelSEO\Services\MetaTagsManager;

final class MetaTagsBuilder
{
    public function __construct(
        private array $config = [],
        private MetaTagsManager $metaTagsManager
    ) {
    }

    /**
     * Get current URL safely (works in console and HTTP contexts)
     */
    private function getCurrentUrl(): string
    {
        if (app()->runningInConsole()) {
            return config('app.url', 'http://localhost');
        }
        
        try {
            return request()->url();
        } catch (\Exception $e) {
            return config('app.url', 'http://localhost');
        }
    }

    /**
     * Get canonical URL - for paginated pages, points to the first page
     */
    /**
     * Get canonical URL - for paginated pages, includes the page parameter
     */
    private function getCanonicalUrl($paginator = null): string
    {
        // 1. If explicit paginator is provided, use its current page URL
        if ($paginator && method_exists($paginator, 'url') && method_exists($paginator, 'currentPage')) {
            try {
                $url = $paginator->url($paginator->currentPage());
                
                // Remove ?page=1 or &page=1 to ensure strict canonical
                // Check if it has query params
                if (str_contains($url, 'page=1')) {
                    $parts = parse_url($url);
                    if (isset($parts['query'])) {
                        parse_str($parts['query'], $query);
                        if (isset($query['page']) && $query['page'] == 1) {
                            unset($query['page']);
                        }
                        
                        // Rebuild URL
                        $newQuery = http_build_query($query);
                        $url = $parts['scheme'] . '://' . $parts['host'] . ($parts['path'] ?? '') . ($newQuery ? '?' . $newQuery : '');
                    }
                }
                
                return $url;
            } catch (\Exception $e) {
                // Fallback
            }
        }

        $currentUrl = $this->getCurrentUrl();
        
        if (app()->runningInConsole()) {
            return $currentUrl;
        }
        
        try {
            // Check if this is a paginated page
            $page = request()->query('page');
            
            // Also check URL path for /page/N pattern
            $path = request()->path();
            if (preg_match('/\/page\/(\d+)/', $path, $matches)) {
                $page = $matches[1];
                // Remove /page/N from URL for canonical
                $currentUrl = preg_replace('/\/page\/\d+/', '', $currentUrl);
            }
            
            // If we are on page 2+, append it to canonical (Standard SEO practice)
            // Unless configured to point to first page (Not recommended for unique archives)
            $paginationConfig = $this->config['pagination'] ?? [];
            $canonicalToFirst = $paginationConfig['canonical_to_first'] ?? false; // Default should be false for proper indexing
            
            if ($page && (int)$page > 1 && !$canonicalToFirst) {
                $separator = str_contains($currentUrl, '?') ? '&' : '?';
                return $currentUrl . $separator . 'page=' . $page;
            }
            
            return $currentUrl;
        } catch (\Exception $e) {
            return $currentUrl;
        }
    }

    /**
     * Check if current page is a paginated page (page > 1)
     */
    private function isPaginatedPage(): bool
    {
        if (app()->runningInConsole()) {
            return false;
        }
        
        try {
            $page = request()->query('page');
            if ($page && (int)$page > 1) {
                return true;
            }
            
            // Check URL path for /page/N pattern
            $path = request()->path();
            if (preg_match('/\/page\/(\d+)/', $path, $matches)) {
                return (int)$matches[1] > 1;
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get robots directive considering pagination
     */
    private function getRobotsDirective(string $defaultRobots): string
    {
        $paginationConfig = $this->config['pagination'] ?? [];
        $noindexPagination = $paginationConfig['noindex_pagination'] ?? false;
        
        if ($noindexPagination && $this->isPaginatedPage()) {
            return 'noindex, follow';
        }
        
        return $defaultRobots;
    }

    public function build(PageData $pageData, string $pageType, $model, array $siteData = [], $paginator = null): void
    {
        $this->metaTagsManager->setTitle($pageData->title);
        $this->metaTagsManager->setDescription($pageData->description);
        
        if (!empty($pageData->keywords)) {
            $keywords = is_array($pageData->keywords) 
                ? implode(', ', $pageData->keywords) 
                : $pageData->keywords;
            $this->metaTagsManager->addMeta('keywords', $keywords);
        }

        $this->metaTagsManager->addMeta('author', $pageData->author);
        
        // Use pagination-aware robots directive
        $robots = $this->getRobotsDirective($pageData->robots);
        $this->metaTagsManager->addMeta('robots', $robots);
        
        $publisher = $siteData['publisher'] ?? $this->config['site']['publisher'] ?? $siteData['name'] ?? $this->config['site']['name'] ?? '';
        if (!empty($publisher)) {
            $this->metaTagsManager->addMeta('publisher', $publisher);
        }

        // Use pagination-aware canonical URL
        $this->metaTagsManager->setCanonical($this->getCanonicalUrl($paginator));

        // Article-specific meta tags
        if ($pageType === 'post' && $model && !empty($pageData->publishedAt)) {
            $this->metaTagsManager->addMeta('article:published_time', $pageData->publishedAt, 'property');
            $this->metaTagsManager->addMeta('article:modified_time', $pageData->modifiedAt ?? $pageData->publishedAt, 'property');
            $this->metaTagsManager->addMeta('article:author', $pageData->author, 'property');
            $this->metaTagsManager->addMeta('article:publisher', $publisher, 'property');
            $this->metaTagsManager->addMeta('contentType', 'post');
            
            if (is_object($model)) {
                $id = $model->id ?? null;
                $slug = $model->slug ?? null;
                
                if ($id) {
                    $this->metaTagsManager->addMeta('postID', (string) $id);
                }
                if ($slug) {
                    $this->metaTagsManager->addMeta('articleSlug', $slug);
                }
                
                // Check for video
                if (isset($model->video_url) && !empty($model->video_url)) {
                    $this->metaTagsManager->addMeta('hasVideo', 'true');
                }
                
                // Article Tags
                if (isset($model->tags) && method_exists($model->tags, 'pluck')) {
                    $tags = $model->tags->pluck('name')->toArray();
                    foreach ($tags as $tag) {
                        $this->metaTagsManager->addMeta('article:tag', $tag, 'property');
                    }
                }
            }
        }
    }
}

