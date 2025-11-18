<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

use Shammaa\LaravelSEO\Data\PageData;

final class CollectionPageSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(PageData $pageData): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $pageData->title,
            'description' => $pageData->description,
            'url' => request()->url(),
        ];
    }
}

