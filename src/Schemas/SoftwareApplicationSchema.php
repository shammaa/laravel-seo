<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class SoftwareApplicationSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
        ];

        // Application Category
        if (!empty($data['applicationCategory'])) {
            $schema['applicationCategory'] = $data['applicationCategory'];
        }

        // Operating System
        if (!empty($data['operatingSystem'])) {
            $os = is_array($data['operatingSystem']) 
                ? $data['operatingSystem'] 
                : [$data['operatingSystem']];
            $schema['operatingSystem'] = $os;
        }

        // Offers
        if (!empty($data['offers'])) {
            $offer = $data['offers'];
            if (is_array($offer)) {
                $schema['offers'] = [
                    '@type' => 'Offer',
                ];

                if (isset($offer['price'])) {
                    $schema['offers']['price'] = $offer['price'];
                }

                if (isset($offer['priceCurrency'])) {
                    $schema['offers']['priceCurrency'] = $offer['priceCurrency'];
                }

                if (isset($offer['availability'])) {
                    $schema['offers']['availability'] = $offer['availability'];
                }

                if (isset($offer['url'])) {
                    $schema['offers']['url'] = $offer['url'];
                }
            } else {
                $schema['offers'] = $offer;
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

        // Screenshot
        if (!empty($data['screenshot'])) {
            $screenshots = is_array($data['screenshot']) 
                ? $data['screenshot'] 
                : [$data['screenshot']];
            $schema['screenshot'] = $screenshots;
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

        // Software Version
        if (!empty($data['softwareVersion'])) {
            $schema['softwareVersion'] = $data['softwareVersion'];
        }

        // Date Published
        if (!empty($data['datePublished'])) {
            $schema['datePublished'] = $data['datePublished'];
        }

        // Author
        if (!empty($data['author'])) {
            $author = $data['author'];
            if (is_string($author)) {
                $schema['author'] = [
                    '@type' => 'Organization',
                    'name' => $author,
                ];
            } elseif (is_array($author)) {
                $schema['author'] = array_merge(
                    ['@type' => 'Organization'],
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

        // File Size
        if (!empty($data['fileSize'])) {
            $schema['fileSize'] = $data['fileSize'];
        }

        // Download URL
        if (!empty($data['downloadUrl'])) {
            $schema['downloadUrl'] = $data['downloadUrl'];
        }

        // Install URL
        if (!empty($data['installUrl'])) {
            $schema['installUrl'] = $data['installUrl'];
        }

        // Software Requirements
        if (!empty($data['softwareRequirements'])) {
            $requirements = is_array($data['softwareRequirements']) 
                ? $data['softwareRequirements'] 
                : [$data['softwareRequirements']];
            $schema['softwareRequirements'] = $requirements;
        }

        // Permissions
        if (!empty($data['permissions'])) {
            $schema['permissions'] = $data['permissions'];
        }

        // Content Rating
        if (!empty($data['contentRating'])) {
            $schema['contentRating'] = $data['contentRating'];
        }

        return $schema;
    }
}

