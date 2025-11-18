<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Services\MetaTagsManager;

final class MultilingualBuilder
{
    public function __construct(
        private array $config = [],
        private MetaTagsManager $metaTagsManager
    ) {
    }

    public function build($model = null, ?string $currentUrl = null): void
    {
        $multilingualConfig = $this->config['multilingual'] ?? [];
        
        if (empty($multilingualConfig['enabled']) || empty($multilingualConfig['locales'])) {
            return;
        }

        $currentUrl = $currentUrl ?? request()->url();
        $currentLocale = app()->getLocale();
        $locales = $multilingualConfig['locales'];
        $defaultLocale = $multilingualConfig['default_locale'] ?? $currentLocale;
        $urlGenerator = $multilingualConfig['url_generator'] ?? null;

        foreach ($locales as $locale) {
            $alternateUrl = $this->generateAlternateUrl($currentUrl, $currentLocale, $locale, $urlGenerator, $model);
            
            if ($alternateUrl) {
                $this->metaTagsManager->addAlternateLanguage($locale, $alternateUrl);
            }
        }

        // Add x-default
        if (!empty($multilingualConfig['x_default'])) {
            $defaultUrl = $this->generateAlternateUrl($currentUrl, $currentLocale, $defaultLocale, $urlGenerator, $model);
            if ($defaultUrl) {
                $this->metaTagsManager->addAlternateLanguage('x-default', $defaultUrl);
            }
        }
    }

    private function generateAlternateUrl(
        string $currentUrl,
        string $currentLocale,
        string $targetLocale,
        ?callable $urlGenerator,
        $model = null
    ): ?string {
        if ($urlGenerator && is_callable($urlGenerator)) {
            return $urlGenerator($targetLocale, $model, $currentUrl);
        }

        // Default: replace locale in URL
        $pattern = '/' . preg_quote($currentLocale, '/') . '/';
        $replacement = '/' . $targetLocale . '/';
        
        if (strpos($currentUrl, '/' . $currentLocale . '/') !== false) {
            return preg_replace($pattern, $replacement, $currentUrl, 1);
        }

        // If no locale in URL, add it
        $parsedUrl = parse_url($currentUrl);
        $path = $parsedUrl['path'] ?? '/';
        
        if ($targetLocale !== $currentLocale) {
            $path = '/' . $targetLocale . $path;
        }
        
        return ($parsedUrl['scheme'] ?? 'https') . '://' . 
               ($parsedUrl['host'] ?? '') . 
               $path . 
               (!empty($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');
    }
}

