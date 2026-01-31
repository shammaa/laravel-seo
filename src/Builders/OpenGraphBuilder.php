<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Data\PageData;
use Shammaa\LaravelSEO\Services\OpenGraphManager;

final class OpenGraphBuilder
{
    public function __construct(
        private array $config = [],
        private OpenGraphManager $openGraphManager
    ) {
    }

    public function build(PageData $pageData, string $pageType, array $siteData): void
    {
        $this->openGraphManager->setTitle($pageData->title)
            ->setDescription($pageData->description)
            ->setUrl($this->getCurrentUrl())
            ->setType($pageType === 'post' ? 'article' : 'website')
            ->addProperty('og:locale', $siteData['locale'] ?? app()->getLocale())
            ->addProperty('og:site_name', $siteData['name']);

        // Add image
        if (!empty($pageData->image)) {
            $imageUrl = $this->normalizeImageUrl($pageData->image);
            
            $this->openGraphManager->addImage(
                $imageUrl,
                (int) ($this->config['image_sizes']['og']['width'] ?? 1200),
                (int) ($this->config['image_sizes']['og']['height'] ?? 630),
                'image/webp',
                $pageData->title
            );
        }

        // Article-specific properties
        if ($pageType === 'post' && !empty($pageData->publishedAt)) {
            $this->openGraphManager->addProperty('article:published_time', $pageData->publishedAt);
            $this->openGraphManager->addProperty('article:modified_time', $pageData->modifiedAt ?? $pageData->publishedAt);
            $this->openGraphManager->addProperty('article:author', $pageData->author);
            
            $publisher = $siteData['publisher'] ?? $this->config['site']['publisher'] ?? $siteData['name'] ?? $this->config['site']['name'] ?? '';
            if (!empty($publisher)) {
                $this->openGraphManager->addProperty('article:publisher', $publisher);
            }
        }
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
        $imagePath = str_replace(['http://', 'https://', '//'], '', $imagePath);
        $imagePath = ltrim($imagePath, '/');
        
        if (strpos($imagePath, '/') !== false) {
            $imagePath = basename($imagePath);
        }

        // Support for image route helper
        if (!$this->isRunningInConsole() && function_exists('route') && ($this->config['image_route'] ?? null)) {
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
     * Check if running in console
     */
    private function isRunningInConsole(): bool
    {
        return app()->runningInConsole();
    }
}

