<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class BrandSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(string $name, ?string $logo = null, ?string $url = null): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Brand',
            'name' => $name,
        ];

        if ($logo) {
            $schema['logo'] = $logo;
        }

        if ($url) {
            $schema['url'] = $url;
        }

        return $schema;
    }
}

