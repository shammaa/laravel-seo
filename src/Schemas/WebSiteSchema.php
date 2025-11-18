<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class WebSiteSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(array $siteData): array
    {
        $searchUrl = $this->config['site']['search_url'] ?? $siteData['url'] . '/search?q={search_term_string}';

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteData['name'],
            'url' => $siteData['url'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => $searchUrl,
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }
}

