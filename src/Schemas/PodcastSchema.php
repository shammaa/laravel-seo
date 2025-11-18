<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class PodcastSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'PodcastSeries',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
        ];

        // Image
        if (!empty($data['image'])) {
            $images = is_array($data['image']) ? $data['image'] : [$data['image']];
            $schema['image'] = $images;
        }

        // Author
        if (!empty($data['author'])) {
            $author = $data['author'];
            if (is_string($author)) {
                $schema['author'] = [
                    '@type' => 'Person',
                    'name' => $author,
                ];
            } elseif (is_array($author)) {
                $schema['author'] = array_merge(
                    ['@type' => 'Person'],
                    $author
                );
            }
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

        // URL
        if (!empty($data['url'])) {
            $schema['url'] = $data['url'];
        }

        // Language
        if (!empty($data['inLanguage'])) {
            $schema['inLanguage'] = $data['inLanguage'];
        }

        // Category
        if (!empty($data['category'])) {
            $categories = is_array($data['category']) 
                ? $data['category'] 
                : [$data['category']];
            $schema['category'] = $categories;
        }

        // Episode
        if (!empty($data['episode'])) {
            $episode = $data['episode'];
            if (is_array($episode)) {
                $schema['episode'] = array_merge(
                    ['@type' => 'PodcastEpisode'],
                    $episode
                );

                // Ensure episode has required fields
                if (empty($schema['episode']['name'])) {
                    $schema['episode']['name'] = $data['name'] ?? '';
                }

                if (empty($schema['episode']['description'])) {
                    $schema['episode']['description'] = $data['description'] ?? '';
                }
            } else {
                $schema['episode'] = $episode;
            }
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

        return $schema;
    }
}

