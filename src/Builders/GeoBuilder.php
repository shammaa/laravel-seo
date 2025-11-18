<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Services\MetaTagsManager;

final class GeoBuilder
{
    public function __construct(
        private array $config = [],
        private MetaTagsManager $metaTagsManager
    ) {
    }

    public function build(): void
    {
        $geoConfig = $this->config['geo_targeting'] ?? [];
        
        if (empty($geoConfig['enabled'])) {
            return;
        }

        // Geo Region
        if (!empty($geoConfig['region'])) {
            $this->metaTagsManager->addMeta('geo.region', $geoConfig['region']);
        }

        // Geo Placename
        if (!empty($geoConfig['placename'])) {
            $this->metaTagsManager->addMeta('geo.placename', $geoConfig['placename']);
        }

        // Geo Position
        if (!empty($geoConfig['latitude']) && !empty($geoConfig['longitude'])) {
            $position = $geoConfig['latitude'] . ';' . $geoConfig['longitude'];
            $this->metaTagsManager->addMeta('geo.position', $position);
            $this->metaTagsManager->addMeta('ICBM', $geoConfig['latitude'] . ', ' . $geoConfig['longitude']);
        }

        // Country
        if (!empty($geoConfig['country'])) {
            $this->metaTagsManager->addMeta('geo.regions', $geoConfig['country']);
        }
    }
}

