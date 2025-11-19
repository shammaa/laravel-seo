<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Data\PageData;
use Shammaa\LaravelSEO\Services\OpenGraphManager;

final class LinkedInCardBuilder
{
    public function __construct(
        private array $config = [],
        private OpenGraphManager $openGraphManager
    ) {
    }

    public function build(PageData $pageData): void
    {
        // LinkedIn uses OpenGraph tags, so we just need to ensure they're set correctly
        // LinkedIn-specific additions can be added here if needed
        
        $linkedInConfig = $this->config['social']['linkedin'] ?? [];
        
        // LinkedIn prefers specific image dimensions
        if (!empty($pageData->image)) {
            $imageUrl = $this->normalizeImageUrl($pageData->image);
            
            // LinkedIn specific meta tags (if needed)
            // These are typically handled by OpenGraph, but we can add LinkedIn-specific ones here
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
            $size = $this->config['image_route']['linkedin_size'] ?? '1200x627';
            
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
     * Check if running in console
     */
    private function isRunningInConsole(): bool
    {
        return app()->runningInConsole();
    }
}

