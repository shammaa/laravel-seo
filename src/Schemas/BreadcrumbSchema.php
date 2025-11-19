<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class BreadcrumbSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build($model, array $siteData, string $pageType = 'post'): array
    {
        $items = $this->getItems($model, $siteData, $pageType);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /**
     * Get breadcrumb items as array (for HTML rendering)
     */
    public function getItems($model, array $siteData, string $pageType = 'post'): array
    {
        $items = [];
        $position = 1;
        $defaults = $this->config['defaults'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];
        
        // Home item
        $homeLabel = $this->config['breadcrumb']['home_label'] ?? 'Home';
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $homeLabel,
            'item' => $siteData['url'],
        ];

        switch ($pageType) {
            case 'post':
                $items = array_merge($items, $this->buildPostBreadcrumbs($model, $position));
                break;

            case 'category':
                $items = array_merge($items, $this->buildCategoryBreadcrumbs($model, $position));
                break;

            case 'product':
                $items = array_merge($items, $this->buildProductBreadcrumbs($model, $position));
                break;

            case 'tag':
                $items = array_merge($items, $this->buildTagBreadcrumbs($model, $position));
                break;

            case 'author':
                $items = array_merge($items, $this->buildAuthorBreadcrumbs($model, $position));
                break;

            case 'archive':
                $items = array_merge($items, $this->buildArchiveBreadcrumbs($model, $position));
                break;

            case 'page':
                $items = array_merge($items, $this->buildPageBreadcrumbs($model, $position));
                break;
        }

        return $items;
    }

    private function buildPostBreadcrumbs($model, int $position): array
    {
        $items = [];
        $defaults = $this->config['defaults'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        if (!is_object($model)) {
            return $items;
        }

        $category = null;
        
        if (isset($model->categories) && method_exists($model->categories, 'isNotEmpty') && $model->categories->isNotEmpty()) {
            $category = $model->categories->first();
        }

        if ($category) {
            // Parent category
            if (!$this->isRunningInConsole() && isset($category->parent) && isset($category->parent_id) && $category->parent_id) {
                $parent = $category->parent;
                if (method_exists($parent, 'route')) {
                    try {
                        $items[] = [
                            '@type' => 'ListItem',
                            'position' => $position++,
                            'name' => $parent->name ?? '',
                            'item' => $parent->route(),
                        ];
                    } catch (\Exception $e) {
                        // Skip if route fails
                    }
                }
            }

            // Category
            if (!$this->isRunningInConsole() && method_exists($category, 'route')) {
                try {
                    $items[] = [
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => $category->name ?? '',
                        'item' => $category->route(),
                    ];
                } catch (\Exception $e) {
                    // Skip if route fails
                }
            }
        }

        // Post title
        $postTitle = $model->title ?? $model->name ?? ($fallbacks['post_title'] ?? 'Post');
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $postTitle,
        ];

        return $items;
    }

    private function buildCategoryBreadcrumbs($model, int $position): array
    {
        $items = [];
        $defaults = $this->config['defaults'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        if (!is_object($model)) {
            return $items;
        }

        // Parent category
        if (!$this->isRunningInConsole() && isset($model->parent) && isset($model->parent_id) && $model->parent_id) {
            $parent = $model->parent;
            if (method_exists($parent, 'route')) {
                try {
                    $items[] = [
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => $parent->name ?? '',
                        'item' => $parent->route(),
                    ];
                } catch (\Exception $e) {
                    // Skip if route fails
                }
            }
        }

        // Category name
        $categoryName = $model->name ?? ($fallbacks['category_name'] ?? 'Category');
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $categoryName,
        ];

        return $items;
    }

    private function buildProductBreadcrumbs($model, int $position): array
    {
        $items = [];
        $defaults = $this->config['defaults'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        if (!is_object($model)) {
            return $items;
        }

        // Category (if product has category)
        if (isset($model->category) && is_object($model->category)) {
            $category = $model->category;
            
            // Parent category
            if (isset($category->parent) && isset($category->parent_id) && $category->parent_id) {
                $parent = $category->parent;
                if (method_exists($parent, 'route')) {
                    try {
                        $items[] = [
                            '@type' => 'ListItem',
                            'position' => $position++,
                            'name' => $parent->name ?? '',
                            'item' => $parent->route(),
                        ];
                    } catch (\Exception $e) {
                        // Skip if route fails
                    }
                }
            }

            // Category
            if (method_exists($category, 'route')) {
                try {
                    $items[] = [
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => $category->name ?? '',
                        'item' => $category->route(),
                    ];
                } catch (\Exception $e) {
                    // Skip if route fails
                }
            }
        }

        // Product name
        $productName = $model->name ?? $model->title ?? $model->product_name ?? ($fallbacks['product_name'] ?? 'Product');
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $productName,
        ];

        return $items;
    }

    private function buildTagBreadcrumbs($model, int $position): array
    {
        $items = [];
        $defaults = $this->config['defaults'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        if (!is_object($model)) {
            return $items;
        }

        // Tag name
        $tagName = $model->name ?? $model->title ?? ($fallbacks['tag_name'] ?? 'Tag');
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $tagName,
        ];

        return $items;
    }

    private function buildAuthorBreadcrumbs($model, int $position): array
    {
        $items = [];
        $defaults = $this->config['defaults'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        if (!is_object($model)) {
            return $items;
        }

        // Author name
        $authorName = $model->name ?? $model->username ?? $model->title ?? ($fallbacks['author_name'] ?? 'Author');
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $authorName,
        ];

        return $items;
    }

    private function buildArchiveBreadcrumbs($model, int $position): array
    {
        $items = [];
        $defaults = $this->config['defaults'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        // Archive can be a string (date) or object
        if (is_string($model)) {
            // Date string like "2024-01" or "January 2024"
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $model,
            ];
        } elseif (is_object($model)) {
            // Archive object with date or name
            $archiveName = $model->name ?? $model->title ?? $model->date ?? ($fallbacks['archive_name'] ?? 'Archive');
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $archiveName,
            ];
        } else {
            // Fallback
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $fallbacks['archive_name'] ?? 'Archive',
            ];
        }

        return $items;
    }

    private function buildPageBreadcrumbs($model, int $position): array
    {
        $items = [];
        $defaults = $this->config['defaults'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        if (!is_object($model)) {
            return $items;
        }

        // Parent page (if exists)
        if (!$this->isRunningInConsole() && isset($model->parent) && isset($model->parent_id) && $model->parent_id) {
            $parent = $model->parent;
            if (method_exists($parent, 'route')) {
                try {
                    $items[] = [
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => $parent->title ?? $parent->name ?? '',
                        'item' => $parent->route(),
                    ];
                } catch (\Exception $e) {
                    // Skip if route fails
                }
            }
        }

        // Page title
        $pageTitle = $model->title ?? $model->name ?? ($fallbacks['page_title'] ?? 'Page');
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $pageTitle,
        ];

        return $items;
    }

    /**
     * Check if running in console
     */
    private function isRunningInConsole(): bool
    {
        return app()->runningInConsole();
    }
}

