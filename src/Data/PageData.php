<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Data;

final class PageData
{
    public function __construct(
        public string $title,
        public string $description,
        public ?string $image = null,
        public string $schema = 'WebPage',
        public array $keywords = [],
        public string $author = '',
        public string $robots = 'index, follow',
        public ?string $publishedAt = null,
        public ?string $modifiedAt = null,
        public $model = null,
    ) {
    }
}

