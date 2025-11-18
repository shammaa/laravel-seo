<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class AggregateRatingSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(
        float $ratingValue,
        int $ratingCount,
        float $bestRating = 5.0,
        float $worstRating = 1.0
    ): array {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'AggregateRating',
            'ratingValue' => $ratingValue,
            'bestRating' => $bestRating,
            'worstRating' => $worstRating,
            'ratingCount' => $ratingCount,
            'reviewCount' => $ratingCount,
        ];
    }
}

