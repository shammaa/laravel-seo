<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class LocalBusinessSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(array $data): array
    {
        $businessType = $data['businessType'] ?? 'LocalBusiness';
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $businessType,
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
        ];

        // Address
        if (!empty($data['address'])) {
            $address = $data['address'];
            $schema['address'] = [
                '@type' => 'PostalAddress',
            ];

            if (isset($address['streetAddress'])) {
                $schema['address']['streetAddress'] = $address['streetAddress'];
            }

            if (isset($address['addressLocality'])) {
                $schema['address']['addressLocality'] = $address['addressLocality'];
            }

            if (isset($address['addressRegion'])) {
                $schema['address']['addressRegion'] = $address['addressRegion'];
            }

            if (isset($address['postalCode'])) {
                $schema['address']['postalCode'] = $address['postalCode'];
            }

            if (isset($address['addressCountry'])) {
                $schema['address']['addressCountry'] = $address['addressCountry'];
            }
        }

        // Geo Coordinates
        if (!empty($data['geo'])) {
            $geo = $data['geo'];
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
            ];

            if (isset($geo['latitude'])) {
                $schema['geo']['latitude'] = (float) $geo['latitude'];
            }

            if (isset($geo['longitude'])) {
                $schema['geo']['longitude'] = (float) $geo['longitude'];
            }
        }

        // Telephone
        if (!empty($data['telephone'])) {
            $schema['telephone'] = $data['telephone'];
        }

        // Email
        if (!empty($data['email'])) {
            $schema['email'] = $data['email'];
        }

        // URL
        if (!empty($data['url'])) {
            $schema['url'] = $data['url'];
        }

        // Logo
        if (!empty($data['logo'])) {
            $schema['logo'] = $data['logo'];
        }

        // Image
        if (!empty($data['image'])) {
            $images = is_array($data['image']) ? $data['image'] : [$data['image']];
            $schema['image'] = $images;
        }

        // Opening Hours
        if (!empty($data['openingHours'])) {
            $hours = is_array($data['openingHours']) 
                ? $data['openingHours'] 
                : [$data['openingHours']];
            $schema['openingHoursSpecification'] = array_map(function ($hour) {
                if (is_string($hour)) {
                    // Simple format: "Mo-Fr 09:00-17:00"
                    return [
                        '@type' => 'OpeningHoursSpecification',
                        'dayOfWeek' => $this->parseDayOfWeek($hour),
                        'opens' => $this->parseOpens($hour),
                        'closes' => $this->parseCloses($hour),
                    ];
                }
                return array_merge(['@type' => 'OpeningHoursSpecification'], $hour);
            }, $hours);
        }

        // Price Range
        if (!empty($data['priceRange'])) {
            $schema['priceRange'] = $data['priceRange'];
        }

        // Payment Accepted
        if (!empty($data['paymentAccepted'])) {
            $payments = is_array($data['paymentAccepted']) 
                ? $data['paymentAccepted'] 
                : [$data['paymentAccepted']];
            $schema['paymentAccepted'] = $payments;
        }

        // Currencies Accepted
        if (!empty($data['currenciesAccepted'])) {
            $currencies = is_array($data['currenciesAccepted']) 
                ? $data['currenciesAccepted'] 
                : [$data['currenciesAccepted']];
            $schema['currenciesAccepted'] = $currencies;
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

        // Serves Cuisine (for restaurants)
        if (!empty($data['servesCuisine'])) {
            $cuisines = is_array($data['servesCuisine']) 
                ? $data['servesCuisine'] 
                : [$data['servesCuisine']];
            $schema['servesCuisine'] = $cuisines;
        }

        // Menu (for restaurants)
        if (!empty($data['menu'])) {
            $schema['menu'] = $data['menu'];
        }

        // HasMap
        if (!empty($data['hasMap'])) {
            $schema['hasMap'] = $data['hasMap'];
        }

        return $schema;
    }

    private function parseDayOfWeek(string $hour): ?string
    {
        if (preg_match('/(Mo|Tu|We|Th|Fr|Sa|Su)(?:-(Mo|Tu|We|Th|Fr|Sa|Su))?/', $hour, $matches)) {
            return $matches[0];
        }
        return null;
    }

    private function parseOpens(string $hour): ?string
    {
        if (preg_match('/(\d{2}:\d{2})/', $hour, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function parseCloses(string $hour): ?string
    {
        if (preg_match('/(\d{2}:\d{2})/', $hour, $matches, PREG_OFFSET_CAPTURE)) {
            if (isset($matches[1][1]) && $matches[1][1] > 5) {
                return $matches[1][0];
            }
        }
        return null;
    }
}

