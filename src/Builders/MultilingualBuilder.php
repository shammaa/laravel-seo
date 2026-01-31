<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Services\MetaTagsManager;

final class MultilingualBuilder
{
    /**
     * Global URL generator callback
     * 
     * @var callable|null
     */
    private static mixed $urlGenerator = null;

    public function __construct(
        private array $config = [],
        private MetaTagsManager $metaTagsManager
    ) {
    }

    /**
     * Register a global custom URL generator
     * 
     * @param callable $callback Function signature: (string $locale, $model, string $currentUrl): string
     * @return void
     */
    public static function urlGeneratorUsing(callable $callback): void
    {
        self::$urlGenerator = $callback;
    }

    public function build($model = null, ?string $currentUrl = null): void
    {
        $multilingualConfig = $this->config['multilingual'] ?? [];
        
        if (empty($multilingualConfig['enabled']) || empty($multilingualConfig['locales'])) {
            return;
        }

        $currentUrl = $currentUrl ?? $this->getCurrentUrl();
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
        // Use global URL generator if set (takes priority)
        if (self::$urlGenerator && is_callable(self::$urlGenerator)) {
            return call_user_func(self::$urlGenerator, $targetLocale, $model, $currentUrl);
        }

        // Fallback to config URL generator
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

    /**
     * Get current URL safely (works in console and HTTP contexts)
     */
    /**
     * Get current URL safely (works in console and HTTP contexts)
     */
    private function getCurrentUrl(): string
    {
        if (app()->runningInConsole()) {
            return config('app.url', 'http://localhost');
        }
        
        try {
            $url = request()->url();
            $page = request()->query('page');
            
            // If on page 2+, append page parameter
            if ($page && (int)$page > 1) {
                $separator = str_contains($url, '?') ? '&' : '?';
                $url .= $separator . 'page=' . $page;
            }
            
            return $url;
        } catch (\Exception $e) {
            return config('app.url', 'http://localhost');
        }
    }
}

