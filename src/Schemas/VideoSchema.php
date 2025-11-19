<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

use Shammaa\LaravelSEO\Data\PageData;

final class VideoSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(PageData $pageData, $model, array $siteData): array
    {
        $thumbnailUrl = $this->getThumbnailUrl($pageData->image);
        $orgConfig = $this->config['organization'] ?? [];
        $siteConfig = $this->config['site'] ?? [];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => $pageData->title,
            'uploadDate' => $pageData->publishedAt,
            'description' => $pageData->description,
            'thumbnailUrl' => $thumbnailUrl,
            'embedUrl' => $model->video_url ?? '',
            'publisher' => [
                '@type' => 'NewsMediaOrganization',
                'name' => $orgConfig['name'] ?? $siteConfig['publisher'] ?? $siteData['name'],
                'url' => $siteData['url'],
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $siteData['logo'],
                ],
            ],
        ];

        if (!empty($orgConfig['same_as'])) {
            $schema['publisher']['sameAs'] = $orgConfig['same_as'];
        }

        return $schema;
    }

    private function getThumbnailUrl(?string $imagePath): string
    {
        if (empty($imagePath)) {
            return asset($this->config['defaults']['image'] ?? 'images/default.jpg');
        }

        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Normalize image path
        $imagePath = str_replace(['http://', 'https://', '//'], '', $imagePath);
        $imagePath = ltrim($imagePath, '/');
        
        if (strpos($imagePath, '/') !== false) {
            $imagePath = basename($imagePath);
        }

        // Support for image route helper
        if (!$this->isRunningInConsole() && function_exists('route') && ($this->config['image_route'] ?? null)) {
            $routeName = $this->config['image_route']['name'] ?? 'image';
            $size = '1920x1440';
            
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

