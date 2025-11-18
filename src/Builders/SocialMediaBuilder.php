<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Services\MetaTagsManager;
use Shammaa\LaravelSEO\Services\OpenGraphManager;

final class SocialMediaBuilder
{
    public function __construct(
        private array $config = [],
        private MetaTagsManager $metaTagsManager,
        private OpenGraphManager $openGraphManager
    ) {
    }

    public function build(): void
    {
        $socialConfig = $this->config['social'] ?? [];

        // Pinterest
        if (!empty($socialConfig['pinterest']['verify'])) {
            $this->metaTagsManager->addMeta('pinterest-site-verification', $socialConfig['pinterest']['verify']);
        }

        // WhatsApp optimization (uses OpenGraph, but ensure images are optimized)
        // WhatsApp prefers square images (1200x1200) for better previews
        if (!empty($socialConfig['whatsapp']['enabled'])) {
            // Add specific meta for WhatsApp if needed
            // WhatsApp primarily uses OpenGraph tags
        }

        // Telegram (uses OpenGraph + Twitter Cards)
        if (!empty($socialConfig['telegram']['enabled'])) {
            // Telegram uses OpenGraph and Twitter Cards
            // No additional tags needed, but we can add Telegram-specific optimizations
        }
    }
}

