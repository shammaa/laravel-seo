<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class BookSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Book',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
        ];

        // Author
        if (!empty($data['author'])) {
            $authors = is_array($data['author']) ? $data['author'] : [$data['author']];
            $schema['author'] = array_map(function ($author) {
                if (is_string($author)) {
                    return [
                        '@type' => 'Person',
                        'name' => $author,
                    ];
                }
                return array_merge(['@type' => 'Person'], $author);
            }, $authors);
        }

        // ISBN
        if (!empty($data['isbn'])) {
            $schema['isbn'] = $data['isbn'];
        }

        // Date Published
        if (!empty($data['datePublished'])) {
            $schema['datePublished'] = $data['datePublished'];
        }

        // Publisher
        if (!empty($data['publisher'])) {
            $publisher = $data['publisher'];
            if (is_string($publisher)) {
                $schema['publisher'] = [
                    '@type' => 'Organization',
                    'name' => $publisher,
                ];
            } elseif (is_array($publisher)) {
                $schema['publisher'] = array_merge(
                    ['@type' => 'Organization'],
                    $publisher
                );
            }
        }

        // Book Format
        if (!empty($data['bookFormat'])) {
            $schema['bookFormat'] = $data['bookFormat'];
        }

        // Number of Pages
        if (!empty($data['numberOfPages'])) {
            $schema['numberOfPages'] = (int) $data['numberOfPages'];
        }

        // Image
        if (!empty($data['image'])) {
            $images = is_array($data['image']) ? $data['image'] : [$data['image']];
            $schema['image'] = $images;
        }

        // URL
        if (!empty($data['url'])) {
            $schema['url'] = $data['url'];
        }

        // Aggregate Rating
        if (!empty($data['aggregateRating'])) {
            $rating = $data['aggregateRating'];
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $rating['ratingValue'] ?? 0,
                'bestRating' => $rating['bestRating'] ?? 5.0,
                'worstRating' => $rating['worstRating'] ?? 1.0,
                'ratingCount' => $rating['ratingCount'] ?? 0,
            ];
        }

        // Reviews
        if (!empty($data['review'])) {
            $reviews = is_array($data['review']) ? $data['review'] : [$data['review']];
            $schema['review'] = array_map(function ($review) {
                if (is_array($review)) {
                    return array_merge(['@type' => 'Review'], $review);
                }
                return $review;
            }, $reviews);
        }

        // Language
        if (!empty($data['inLanguage'])) {
            $schema['inLanguage'] = $data['inLanguage'];
        }

        // Genre
        if (!empty($data['genre'])) {
            $genres = is_array($data['genre']) ? $data['genre'] : [$data['genre']];
            $schema['genre'] = $genres;
        }

        // Book Edition
        if (!empty($data['bookEdition'])) {
            $schema['bookEdition'] = $data['bookEdition'];
        }

        return $schema;
    }
}

