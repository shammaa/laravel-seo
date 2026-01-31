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

    public function build(PageData $pageData, string $pageType, $model, array $siteData = []): void
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
        $this->metaTagsManager->addMeta('robots', $pageData->robots);
        
        $publisher = $this->config['site']['publisher'] ?? $siteData['name'] ?? $this->config['site']['name'] ?? '';
        if (!empty($publisher)) {
            $this->metaTagsManager->addMeta('publisher', $publisher);
        }

        $this->metaTagsManager->setCanonical($this->getCurrentUrl());

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

