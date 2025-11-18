<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class MovieSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Movie',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
        ];

        // Image
        if (!empty($data['image'])) {
            $images = is_array($data['image']) ? $data['image'] : [$data['image']];
            $schema['image'] = $images;
        }

        // Date Published
        if (!empty($data['datePublished'])) {
            $schema['datePublished'] = $data['datePublished'];
        }

        // Director
        if (!empty($data['director'])) {
            $directors = is_array($data['director']) ? $data['director'] : [$data['director']];
            $schema['director'] = array_map(function ($director) {
                if (is_string($director)) {
                    return [
                        '@type' => 'Person',
                        'name' => $director,
                    ];
                }
                return array_merge(['@type' => 'Person'], $director);
            }, $directors);
        }

        // Actor
        if (!empty($data['actor'])) {
            $actors = is_array($data['actor']) ? $data['actor'] : [$data['actor']];
            $schema['actor'] = array_map(function ($actor) {
                if (is_string($actor)) {
                    return [
                        '@type' => 'Person',
                        'name' => $actor,
                    ];
                }
                return array_merge(['@type' => 'Person'], $actor);
            }, $actors);
        }

        // Genre
        if (!empty($data['genre'])) {
            $genres = is_array($data['genre']) ? $data['genre'] : [$data['genre']];
            $schema['genre'] = $genres;
        }

        // Duration (ISO 8601)
        if (!empty($data['duration'])) {
            $schema['duration'] = $data['duration'];
        }

        // Aggregate Rating
        if (!empty($data['aggregateRating'])) {
            $rating = $data['aggregateRating'];
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $rating['ratingValue'] ?? 0,
                'bestRating' => $rating['bestRating'] ?? 10.0,
                'worstRating' => $rating['worstRating'] ?? 1.0,
                'ratingCount' => $rating['ratingCount'] ?? 0,
            ];
        }

        // Content Rating
        if (!empty($data['contentRating'])) {
            $schema['contentRating'] = $data['contentRating'];
        }

        // Production Company
        if (!empty($data['productionCompany'])) {
            $companies = is_array($data['productionCompany']) 
                ? $data['productionCompany'] 
                : [$data['productionCompany']];
            $schema['productionCompany'] = array_map(function ($company) {
                if (is_string($company)) {
                    return [
                        '@type' => 'Organization',
                        'name' => $company,
                    ];
                }
                return array_merge(['@type' => 'Organization'], $company);
            }, $companies);
        }

        // Country of Origin
        if (!empty($data['countryOfOrigin'])) {
            $countries = is_array($data['countryOfOrigin']) 
                ? $data['countryOfOrigin'] 
                : [$data['countryOfOrigin']];
            $schema['countryOfOrigin'] = $countries;
        }

        // Language
        if (!empty($data['inLanguage'])) {
            $languages = is_array($data['inLanguage']) 
                ? $data['inLanguage'] 
                : [$data['inLanguage']];
            $schema['inLanguage'] = $languages;
        }

        // URL
        if (!empty($data['url'])) {
            $schema['url'] = $data['url'];
        }

        // Trailer
        if (!empty($data['trailer'])) {
            $trailer = $data['trailer'];
            if (is_string($trailer)) {
                $schema['trailer'] = [
                    '@type' => 'VideoObject',
                    'name' => 'Trailer',
                    'embedUrl' => $trailer,
                ];
            } elseif (is_array($trailer)) {
                $schema['trailer'] = array_merge(
                    ['@type' => 'VideoObject'],
                    $trailer
                );
            }
        }

        return $schema;
    }
}

