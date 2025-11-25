<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Services\MetaTagsManager;

final class MultilingualBuilder
{
    /**
     * Set a callback that should be used create custom URL.
     *
     * @var (\Closure(string, \Illuminate\Database\Eloquent\Model|null, string): string)|null
     */
    protected static $urlGeneratorCallback;

    public function __construct(
        private array $config,
        private MetaTagsManager $metaTagsManager
    ) {}

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

        foreach ($locales as $locale) {
            $alternateUrl = $this->generateAlternateUrl($currentUrl, $currentLocale, $locale, $model);

            if ($alternateUrl) {
                $this->metaTagsManager->addAlternateLanguage($locale, $alternateUrl);
            }
        }

        // Add x-default
        if (!empty($multilingualConfig['x_default'])) {
            $defaultUrl = $this->generateAlternateUrl($currentUrl, $currentLocale, $defaultLocale, $model);
            if ($defaultUrl) {
                $this->metaTagsManager->addAlternateLanguage('x-default', $defaultUrl);
            }
        }
    }

    /**
     * Set a callback that should be used creating custom URL.
     *
     * @param  \Closure(string, \Illuminate\Database\Eloquent\Model|null, string): string  $callback
     */
    public static function urlGeneratorUsing(callable $callback): void
    {
        self::$urlGeneratorCallback = $callback;
    }

    /**
     * Get a callback that should be used creating custom URL.
     *
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     */
    protected function urlGenerator(string $targetLocale, $model, string $currentUrl): string
    {
        return call_user_func(self::$urlGeneratorCallback, $targetLocale, $model, $currentUrl);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     */
    private function generateAlternateUrl(
        string $currentUrl,
        string $currentLocale,
        string $targetLocale,
        $model = null
    ): ?string {
        if (is_callable(self::$urlGeneratorCallback)) {
            return $this->urlGenerator($targetLocale, $model, $currentUrl);
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
    private function getCurrentUrl(): string
    {
        if (app()->runningInConsole()) {
            return config('app.url', 'http://localhost');
        }

        try {
            return request()->url();
        } catch (\Exception $e) {
            return config('app.url', 'http://localhost');
        }
    }
}
