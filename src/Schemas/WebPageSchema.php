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
            'url' => request()->url(),
            'speakable' => [
                '@type' => 'SpeakableSpecification',
                'cssSelector' => ['.article-header'],
            ],
        ];
    }
}

