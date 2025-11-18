<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Illuminate\Support\Facades\View;

final class PerformanceBuilder
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(): string
    {
        $performanceConfig = $this->config['performance'] ?? [];
        $html = '';

        // DNS Prefetch
        if (!empty($performanceConfig['dns_prefetch'])) {
            foreach ($performanceConfig['dns_prefetch'] as $domain) {
                $html .= '<link rel="dns-prefetch" href="//' . $domain . '">' . PHP_EOL;
            }
        }

        // Preconnect
        if (!empty($performanceConfig['preconnect'])) {
            foreach ($performanceConfig['preconnect'] as $url) {
                $html .= '<link rel="preconnect" href="' . $url . '" crossorigin>' . PHP_EOL;
            }
        }

        // Preload
        if (!empty($performanceConfig['preload'])) {
            foreach ($performanceConfig['preload'] as $resource) {
                $as = $resource['as'] ?? 'script';
                $href = $resource['href'] ?? '';
                $type = $resource['type'] ?? '';
                $onload = $resource['onload'] ?? null;
                
                if ($href) {
                    $link = '<link rel="preload" as="' . $as . '" href="' . $href . '"';
                    if ($type) {
                        $link .= ' type="' . $type . '"';
                    }
                    if ($onload) {
                        $link .= ' onload="' . e($onload) . '"';
                    }
                    $link .= '>' . PHP_EOL;
                    $html .= $link;
                }
            }
        }

        // Prefetch
        if (!empty($performanceConfig['prefetch'])) {
            foreach ($performanceConfig['prefetch'] as $url) {
                $html .= '<link rel="prefetch" href="' . e($url) . '">' . PHP_EOL;
            }
        }

        // Prerender
        if (!empty($performanceConfig['prerender'])) {
            foreach ($performanceConfig['prerender'] as $url) {
                $html .= '<link rel="prerender" href="' . e($url) . '">' . PHP_EOL;
            }
        }

        // Modulepreload
        if (!empty($performanceConfig['modulepreload'])) {
            foreach ($performanceConfig['modulepreload'] as $resource) {
                $href = is_array($resource) ? ($resource['href'] ?? '') : $resource;
                $type = is_array($resource) ? ($resource['type'] ?? '') : '';
                
                if ($href) {
                    $link = '<link rel="modulepreload" href="' . e($href) . '"';
                    if ($type) {
                        $link .= ' type="' . e($type) . '"';
                    }
                    $link .= '>' . PHP_EOL;
                    $html .= $link;
                }
            }
        }

        return $html;
    }
}

