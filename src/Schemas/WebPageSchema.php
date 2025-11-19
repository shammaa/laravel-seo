<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

use Shammaa\LaravelSEO\Data\PageData;

final class WebPageSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(PageData $pageData): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $pageData->title,
            'url' => $this->getCurrentUrl(),
            'speakable' => [
                '@type' => 'SpeakableSpecification',
                'cssSelector' => ['.article-header'],
            ],
        ];
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
}

