<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class CourseSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
        ];

        if (!empty($data['provider'])) {
            $provider = $data['provider'];
            if (is_string($provider)) {
                $schema['provider'] = [
                    '@type' => 'Organization',
                    'name' => $provider,
                ];
            } elseif (is_array($provider)) {
                $schema['provider'] = array_merge(
                    ['@type' => 'Organization'],
                    $provider
                );
            }
        }

        if (!empty($data['courseCode'])) {
            $schema['courseCode'] = $data['courseCode'];
        }

        if (!empty($data['educationalLevel'])) {
            $schema['educationalLevel'] = $data['educationalLevel'];
        }

        if (!empty($data['inLanguage'])) {
            $schema['inLanguage'] = $data['inLanguage'];
        }

        if (!empty($data['image'])) {
            $schema['image'] = $data['image'];
        }

        if (!empty($data['url'])) {
            $schema['url'] = $data['url'];
        }

        // Course Instance
        if (!empty($data['hasCourseInstance'])) {
            $instance = $data['hasCourseInstance'];
            $courseInstance = [
                '@type' => 'CourseInstance',
            ];

            if (!empty($instance['startDate'])) {
                $courseInstance['startDate'] = $instance['startDate'];
            }

            if (!empty($instance['endDate'])) {
                $courseInstance['endDate'] = $instance['endDate'];
            }

            if (!empty($instance['courseMode'])) {
                $courseInstance['courseMode'] = $instance['courseMode'];
            }

            if (!empty($instance['instructor'])) {
                $instructor = $instance['instructor'];
                if (is_string($instructor)) {
                    $courseInstance['instructor'] = [
                        '@type' => 'Person',
                        'name' => $instructor,
                    ];
                } elseif (is_array($instructor)) {
                    $courseInstance['instructor'] = array_merge(
                        ['@type' => 'Person'],
                        $instructor
                    );
                }
            }

            if (!empty($instance['location'])) {
                $courseInstance['location'] = $instance['location'];
            }

            $schema['hasCourseInstance'] = $courseInstance;
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

