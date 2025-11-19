<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

use Shammaa\LaravelSEO\Data\PageData;
use Shammaa\LaravelSEO\Helpers\ReadingTimeCalculator;

final class NewsArticleSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(PageData $pageData, $model, array $siteData): array
    {
        $images = $this->buildImages($pageData->image);
        $author = $this->buildAuthor($model, $siteData);
        $publisher = $this->buildPublisher($siteData);

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'mainEntityOfPage' => $this->getCurrentUrl(),
            'headline' => $pageData->title,
            'description' => $pageData->description,
            'image' => $images,
            'datePublished' => $pageData->publishedAt,
            'dateModified' => $pageData->modifiedAt ?? $pageData->publishedAt,
            'author' => $author,
            'publisher' => $publisher,
        ];

        // Add wordCount if content is available
        if (is_object($model) && isset($model->content)) {
            $wordCount = $this->calculateWordCount($model->content);
            if ($wordCount > 0) {
                $schema['wordCount'] = $wordCount;
            }
            
            // Add timeRequired (Reading Time) if enabled
            if (!empty($this->config['reading_time']['enabled'])) {
                $timeRequired = ReadingTimeCalculator::toIso8601(
                    $model->content,
                    $this->config['reading_time']['words_per_minute'] ?? 200
                );
                $schema['timeRequired'] = $timeRequired;
            }
        }

        return $schema;
    }

    private function buildImages(?string $imagePath): array
    {
        $images = [];
        
        if (empty($imagePath)) {
            return $images;
        }

        // Normalize image path - preserve full path if image_route is configured
        $useImageRoute = function_exists('route') && ($this->config['image_route'] ?? null);
        
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            // Full URL - extract path
            $parsedUrl = parse_url($imagePath);
            $imagePath = ltrim($parsedUrl['path'] ?? '', '/');
        }

        // Remove protocol if present
        $imagePath = str_replace(['http://', 'https://', '//'], '', $imagePath);
        $imagePath = ltrim($imagePath, '/');
        
        // If using image route, we can use basename (route will handle it)
        // Otherwise, keep full path for asset()
        if ($useImageRoute && strpos($imagePath, '/') !== false) {
            $imagePath = basename($imagePath);
        }
        // If not using image route, keep full path (e.g., 'storage/oc.webp')

        $sizes = $this->config['image_sizes']['schema'] ?? [
            ['width' => 1920, 'height' => 1440],
            ['width' => 1920, 'height' => 1080],
            ['width' => 1800, 'height' => 1800],
            ['width' => 1200, 'height' => 630],
        ];

        $addedUrls = [];
        foreach ($sizes as $size) {
            try {
                $imageUrl = $this->getImageUrl($imagePath, $size['width'] . 'x' . $size['height']);
                
                // Avoid duplicate URLs
                if (in_array($imageUrl, $addedUrls)) {
                    continue;
                }
                
                $addedUrls[] = $imageUrl;
                
                $images[] = [
                    '@type' => 'ImageObject',
                    'url' => $imageUrl,
                    'width' => $size['width'],
                    'height' => $size['height'],
                ];
            } catch (\Exception $e) {
                // Skip invalid images
            }
        }

        return $images;
    }

    private function buildAuthor($model, array $siteData): array
    {
        if (is_object($model) && isset($model->writer) && $model->writer) {
            $writerImageUrl = null;
            
            if (!empty($model->writer->photo)) {
                try {
                    $writerImageUrl = $this->getImageUrl($model->writer->photo, '400x400');
                } catch (\Exception $e) {
                    $writerImageUrl = null;
                }
            }

            $authorSchema = [
                '@type' => 'Person',
                'name' => $model->writer->name ?? $siteData['name'],
            ];

            if (!$this->isRunningInConsole() && isset($model->writer->id) && method_exists($model->writer, 'route')) {
                try {
                    $authorSchema['url'] = $model->writer->route();
                } catch (\Exception $e) {
                    $authorSchema['url'] = $siteData['url'];
                }
            } else {
                $authorSchema['url'] = $siteData['url'];
            }

            if ($writerImageUrl) {
                $authorSchema['image'] = $writerImageUrl;
            }

            return $authorSchema;
        }

        // Default to organization
        $orgConfig = $this->config['organization'] ?? [];
        
        return [
            '@type' => 'NewsMediaOrganization',
            'name' => $orgConfig['name'] ?? $siteData['name'],
            'url' => $siteData['url'],
            'sameAs' => $orgConfig['same_as'] ?? [],
        ];
    }

    private function buildPublisher(array $siteData): array
    {
        $orgConfig = $this->config['organization'] ?? [];
        $siteConfig = $this->config['site'] ?? [];

        $publisher = [
            '@type' => 'NewsMediaOrganization',
            'name' => $orgConfig['name'] ?? $siteConfig['publisher'] ?? $siteData['name'],
            'url' => $siteData['url'],
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $siteData['logo'],
                'width' => $orgConfig['logo_width'] ?? 265,
                'height' => $orgConfig['logo_height'] ?? 85,
            ],
        ];

        if (!empty($orgConfig['same_as'])) {
            $publisher['sameAs'] = $orgConfig['same_as'];
        }

        return $publisher;
    }

    private function getImageUrl(string $path, string $size): string
    {
        if (!$this->isRunningInConsole() && function_exists('route') && ($this->config['image_route'] ?? null)) {
            $routeName = $this->config['image_route']['name'] ?? 'image';
            
            try {
                return route($routeName, [
                    'size' => $size,
                    'path' => $path
                ]);
            } catch (\Exception $e) {
                // Fallback to asset
            }
        }

        return asset($path);
    }

    private function calculateWordCount($content): int
    {
        $text = strip_tags($content);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        if (empty($text)) {
            return 0;
        }
        
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        return count($words);
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

