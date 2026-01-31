<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class OrganizationSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(array $siteData): array
    {
        $orgConfig = $this->config['organization'] ?? [];
        $siteConfig = $this->config['site'] ?? [];

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsMediaOrganization',
            '@id' => $siteData['url'] . '#organization',
            'name' => $orgConfig['name'] ?? $siteConfig['publisher'] ?? $siteData['name'],
            'url' => $siteData['url'],
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $siteData['logo'],
                'width' => $orgConfig['logo_width'] ?? 265,
                'height' => $orgConfig['logo_height'] ?? 85,
            ],
        ];

        if (!empty($orgConfig['alternate_name'])) {
            $schema['alternateName'] = $orgConfig['alternate_name'];
        }

        if (!empty($orgConfig['description'])) {
            $schema['description'] = $orgConfig['description'];
        }

        // Use sameAs from siteData (dynamic) or config (static)
        $sameAs = $siteData['same_as'] ?? $orgConfig['same_as'] ?? [];
        if (!empty($sameAs)) {
            $schema['sameAs'] = $sameAs;
        }

        if (!empty($orgConfig['contact_point']['email'])) {
            $schema['contactPoint'] = [
                '@type' => 'ContactPoint',
                'contactType' => $orgConfig['contact_point']['contact_type'] ?? 'customer service',
                'email' => $orgConfig['contact_point']['email'],
                'availableLanguage' => $orgConfig['contact_point']['available_language'] ?? ['Arabic', 'English'],
                'areaServed' => $orgConfig['contact_point']['area_served'] ?? 'SY',
            ];
        }

        if (!empty($orgConfig['address']['address_country'])) {
            $schema['address'] = [
                '@type' => 'PostalAddress',
                'addressCountry' => $orgConfig['address']['address_country'],
                'addressLocality' => $orgConfig['address']['address_locality'] ?? '',
            ];
        }

        if (!empty($orgConfig['founding_date'])) {
            $schema['foundingDate'] = $orgConfig['founding_date'];
        }

        if (!empty($orgConfig['publishing_principles'])) {
            $schema['publishingPrinciples'] = $orgConfig['publishing_principles'];
        }

        return $schema;
    }
}

