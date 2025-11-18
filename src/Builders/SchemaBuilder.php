<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Builders;

use Shammaa\LaravelSEO\Data\PageData;
use Shammaa\LaravelSEO\Schemas\NewsArticleSchema;
use Shammaa\LaravelSEO\Schemas\WebPageSchema;
use Shammaa\LaravelSEO\Schemas\BreadcrumbSchema;
use Shammaa\LaravelSEO\Schemas\VideoSchema;
use Shammaa\LaravelSEO\Schemas\WebSiteSchema;
use Shammaa\LaravelSEO\Schemas\OrganizationSchema;
use Shammaa\LaravelSEO\Schemas\CollectionPageSchema;
use Shammaa\LaravelSEO\Schemas\ProductSchema;

final class SchemaBuilder
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(string $pageType, PageData $pageData, $model, array $siteData): string
    {
        $schemas = [];

        if ($pageType === 'post' && $model) {
            $schemas[] = (new NewsArticleSchema($this->config))->build($pageData, $model, $siteData);
            $schemas[] = (new WebPageSchema($this->config))->build($pageData);
            $schemas[] = (new BreadcrumbSchema($this->config))->build($model, $siteData, 'post');
            
            if (is_object($model) && isset($model->video_url) && !empty($model->video_url)) {
                $schemas[] = (new VideoSchema($this->config))->build($pageData, $model, $siteData);
            }
        } elseif ($pageType === 'product' && $model) {
            $schemas[] = (new ProductSchema($this->config))->build($pageData, $model, $siteData);
            $schemas[] = (new WebPageSchema($this->config))->build($pageData);
            $schemas[] = (new BreadcrumbSchema($this->config))->build($model, $siteData, 'product');
        } elseif ($pageType === 'home') {
            $schemas[] = (new WebSiteSchema($this->config))->build($siteData);
            $schemas[] = (new OrganizationSchema($this->config))->build($siteData);
        } elseif ($pageType === 'category' && $model) {
            $schemas[] = (new CollectionPageSchema($this->config))->build($pageData);
            $schemas[] = (new BreadcrumbSchema($this->config))->build($model, $siteData, 'category');
        }

        return $this->renderSchemas($schemas);
    }

    private function renderSchemas(array $schemas): string
    {
        $html = '';
        foreach ($schemas as $schema) {
            if (empty($schema)) {
                continue;
            }
            
            $html .= '<script type="application/ld+json">';
            $html .= json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $html .= '</script>' . PHP_EOL;
        }
        return $html;
    }
}

