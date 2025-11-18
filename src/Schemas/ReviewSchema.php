<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class ReviewSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(
        string $itemName,
        float $ratingValue,
        float $bestRating = 5.0,
        ?string $reviewBody = null,
        ?string $authorName = null,
        ?string $datePublished = null
    ): array {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'itemReviewed' => [
                '@type' => 'Thing',
                'name' => $itemName,
            ],
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => (string) $ratingValue,
                'bestRating' => (string) $bestRating,
            ],
        ];

        if ($reviewBody) {
            $schema['reviewBody'] = $reviewBody;
        }

        if ($authorName) {
            $schema['author'] = [
                '@type' => 'Person',
                'name' => $authorName,
            ];
        }

        if ($datePublished) {
            $schema['datePublished'] = $datePublished;
        }

        return $schema;
    }
}

