<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Services\MetaTagsManager;

final class SecurityBuilder
{
    public function __construct(
        private array $config = [],
        private MetaTagsManager $metaTagsManager
    ) {
    }

    public function build(): void
    {
        $securityConfig = $this->config['security'] ?? [];

        // Content Security Policy
        if (!empty($securityConfig['content_security_policy'])) {
            $this->metaTagsManager->addMeta('Content-Security-Policy', 
                $securityConfig['content_security_policy'], 
                'http-equiv');
        }

        // Referrer Policy
        if (!empty($securityConfig['referrer_policy'])) {
            $this->metaTagsManager->addMeta('referrer', $securityConfig['referrer_policy']);
        }

        // X-Frame-Options
        if (!empty($securityConfig['x_frame_options'])) {
            $this->metaTagsManager->addMeta('X-Frame-Options', 
                $securityConfig['x_frame_options'], 
                'http-equiv');
        }

        // X-Content-Type-Options
        if (!empty($securityConfig['x_content_type_options'])) {
            $this->metaTagsManager->addMeta('X-Content-Type-Options', 
                $securityConfig['x_content_type_options'], 
                'http-equiv');
        }
    }
}

