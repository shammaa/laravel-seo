<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

use Shammaa\LaravelSEO\Data\PageData;

final class ProfilePageSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(PageData $pageData): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ProfilePage',
            'name' => $pageData->title,
            'description' => $pageData->description,
            'url' => $this->getCurrentUrl(),
            'mainEntity' => [
                '@type' => 'Person',
                'name' => $pageData->author,
                'image' => $pageData->image,
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
            $url = request()->url();
            $page = request()->query('page');
            
            // If on page 2+, append page parameter
            if ($page && (int)$page > 1) {
                $separator = str_contains($url, '?') ? '&' : '?';
                $url .= $separator . 'page=' . $page;
            }
            
            return $url;
        } catch (\Exception $e) {
            return config('app.url', 'http://localhost');
        }
    }
}
