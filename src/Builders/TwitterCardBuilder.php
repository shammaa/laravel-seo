<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Data\PageData;
use Shammaa\LaravelSEO\Helpers\ReadingTimeCalculator;
use Shammaa\LaravelSEO\Services\TwitterCardManager;

final class TwitterCardBuilder
{
    public function __construct(
        private array $config = [],
        private TwitterCardManager $twitterCardManager
    ) {
    }

    public function build(PageData $pageData, $model = null): void
    {
        $twitterConfig = $this->config['social']['twitter'] ?? [];
        
        $image = $this->getImageUrl($pageData->image);
        
        $this->twitterCardManager->setType($twitterConfig['card_type'] ?? 'summary_large_image')
            ->setTitle($pageData->title)
            ->setDescription($pageData->description)
            ->setImage($image);

        // Twitter site and creator
        if (!empty($twitterConfig['site'])) {
            $this->twitterCardManager->addValue('site', $twitterConfig['site']);
        }
        
        if (!empty($twitterConfig['creator'])) {
            $this->twitterCardManager->addValue('creator', $twitterConfig['creator']);
        }

        $this->twitterCardManager->addValue('image:alt', $pageData->title);
        
        // Reading Time (if enabled and model has content)
        if (!empty($this->config['reading_time']['enabled']) && $model && is_object($model)) {
            if (isset($model->content) && !empty($model->content)) {
                $readingTime = ReadingTimeCalculator::format(
                    $model->content,
                    $this->config['reading_time']['words_per_minute'] ?? 200,
                    app()->getLocale(),
                    $this->config['reading_time']['translations'] ?? null
                );
                
                $this->twitterCardManager->addValue('label1', 'Reading time');
                $this->twitterCardManager->addValue('data1', $readingTime);
            }
        }
    }

    private function getImageUrl(?string $imagePath): string
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
            $size = $this->config['image_route']['twitter_size'] ?? '1200x630';
            
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

