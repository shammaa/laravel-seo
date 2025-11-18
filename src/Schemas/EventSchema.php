<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class EventSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(
        string $name,
        string $startDate,
        ?string $endDate = null,
        ?string $description = null,
        ?string $locationName = null,
        ?string $locationAddress = null,
        ?string $image = null,
        ?string $organizerName = null,
        ?string $organizerUrl = null
    ): array {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $name,
            'startDate' => $startDate,
        ];

        if ($endDate) {
            $schema['endDate'] = $endDate;
        }

        if ($description) {
            $schema['description'] = $description;
        }

        if ($image) {
            $schema['image'] = $image;
        }

        if ($locationName || $locationAddress) {
            $location = ['@type' => 'Place'];
            
            if ($locationName) {
                $location['name'] = $locationName;
            }
            
            if ($locationAddress) {
                $location['address'] = [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $locationAddress,
                ];
            }
            
            $schema['location'] = $location;
        }

        if ($organizerName) {
            $organizer = [
                '@type' => 'Organization',
                'name' => $organizerName,
            ];
            
            if ($organizerUrl) {
                $organizer['url'] = $organizerUrl;
            }
            
            $schema['organizer'] = $organizer;
        }

        return $schema;
    }
}

