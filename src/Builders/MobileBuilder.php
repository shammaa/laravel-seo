<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Services\MetaTagsManager;

final class MobileBuilder
{
    public function __construct(
        private array $config = [],
        private MetaTagsManager $metaTagsManager
    ) {
    }

    public function build(): void
    {
        $mobileConfig = $this->config['mobile'] ?? [];

        // Theme Color
        if (!empty($mobileConfig['theme_color'])) {
            $this->metaTagsManager->addMeta('theme-color', $mobileConfig['theme_color']);
        }

        // Apple Mobile Web App
        if (!empty($mobileConfig['apple_mobile_web_app'])) {
            $this->metaTagsManager->addMeta('apple-mobile-web-app-capable', 'yes');
            
            if (!empty($mobileConfig['apple_mobile_web_app']['status_bar_style'])) {
                $this->metaTagsManager->addMeta('apple-mobile-web-app-status-bar-style', 
                    $mobileConfig['apple_mobile_web_app']['status_bar_style']);
            }
            
            if (!empty($mobileConfig['apple_mobile_web_app']['title'])) {
                $this->metaTagsManager->addMeta('apple-mobile-web-app-title', 
                    $mobileConfig['apple_mobile_web_app']['title']);
            }
        }

        // Manifest
        if (!empty($mobileConfig['manifest'])) {
            $this->metaTagsManager->addMeta('manifest', $mobileConfig['manifest'], 'link');
        }
    }
}

