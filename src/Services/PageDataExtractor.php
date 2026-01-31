<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Services;

use Shammaa\LaravelSEO\Data\PageData;
use Carbon\Carbon;
use Illuminate\Support\Str;

final class PageDataExtractor
{
    public function __construct(
        private array $config = []
    ) {
    }

    private function getPageSuffix(): string
    {
        if (app()->runningInConsole()) {
            return '';
        }

        $page = request()->query('page');
        if ($page && (int)$page > 1) {
            return ' - ' . trans('trans.page', [], app()->getLocale()) . ' ' . $page;
        }

        return '';
    }

    public function extract(string $pageType, $model, array $siteData): PageData
    {
        return match ($pageType) {
            'home' => $this->extractHomeData($siteData),
            'post' => $this->extractPostData($model, $siteData),
            'category' => $this->extractCategoryData($model, $siteData),
            'product' => $this->extractProductData($model, $siteData),
            'search' => $this->extractSearchData($model, $siteData),
            'tag' => $this->extractTagData($model, $siteData),
            'author' => $this->extractAuthorData($model, $siteData),
            'archive' => $this->extractArchiveData($model, $siteData),
            'page' => $this->extractPageData($model, $siteData),
            default => $this->extractHomeData($siteData),
        };
    }

    private function extractHomeData(array $siteData): PageData
    {
        $defaults = $this->config['defaults'] ?? [];
        $homeConfig = $this->config['pages']['home'] ?? [];

        return new PageData(
            title: htmlspecialchars_decode($homeConfig['title'] ?? trans('trans.home', [], app()->getLocale()) . ' - ' . $siteData['name']),
            description: htmlspecialchars_decode($homeConfig['description'] ?? $siteData['description']),
            image: $homeConfig['image'] ?? $siteData['logo'],
            schema: $homeConfig['schema'] ?? 'WebSite',
            keywords: $homeConfig['keywords'] ?? $defaults['keywords'] ?? [],
            author: $homeConfig['author'] ?? $siteData['name'],
            robots: $homeConfig['robots'] ?? 'index, follow',
        );
    }

    private function extractPostData($model, array $siteData): PageData
    {
        $defaults = $this->config['defaults'] ?? [];
        $postConfig = $this->config['pages']['post'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        $title = $this->getModelAttribute($model, ['title', 'name'], $fallbacks['post_title'] ?? 'Post');
        $title = htmlspecialchars_decode($title);
        
        $description = $this->limitWords(
            $this->getModelAttribute($model, ['content', 'text', 'description'], ''),
            $postConfig['description_limit'] ?? 30
        );
        $image = $this->getModelAttribute($model, ['photo', 'image', 'thumbnail'], null);
        
        $keywords = $this->extractKeywords($model, $defaults);
        
        $author = $this->getAuthor($model, $siteData);
        
        $publishedAt = $this->getModelAttribute($model, ['created_at', 'published_at'], now());
        $modifiedAt = $this->getModelAttribute($model, ['updated_at', 'modified_at'], now());

        return new PageData(
            title: ($postConfig['title_prefix'] ?? true) 
                ? $title . ' - ' . $siteData['name'] 
                : $title,
            description: $description,
            image: $image,
            schema: 'NewsArticle',
            keywords: $keywords,
            author: $author,
            robots: $postConfig['robots'] ?? 'index, follow',
            publishedAt: Carbon::parse($publishedAt)->toIso8601String(),
            modifiedAt: Carbon::parse($modifiedAt)->toIso8601String(),
            model: $model,
        );
    }

    private function extractCategoryData($model, array $siteData): PageData
    {
        $defaults = $this->config['defaults'] ?? [];
        $categoryConfig = $this->config['pages']['category'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        $name = $this->getModelAttribute($model, ['name', 'title'], $fallbacks['category_name'] ?? 'Category');
        $name = htmlspecialchars_decode($name);
        
        $description = $this->getModelAttribute($model, ['description'], null);
        
        if (empty($description)) {
            $descriptionTemplate = $fallbacks['category_description'] ?? 'Latest news in :name category';
            $description = str_replace(':name', $name, $descriptionTemplate);
        } else {
            $description = $this->limitWords($description, $categoryConfig['description_limit'] ?? 30);
        }

        $pageSuffix = $this->getPageSuffix();
        $title = (($categoryConfig['title_prefix'] ?? true)
                ? $name . ' - ' . $siteData['name']
                : $name) . $pageSuffix;
        $description .= $pageSuffix;

        $image = $this->getModelAttribute($model, ['photo', 'image', 'thumbnail'], null);
        $keywords = $this->extractCategoryKeywords($model, $siteData, $defaults);

        return new PageData(
            title: $title,
            description: $description,
            image: $image ?? $siteData['logo'],
            schema: $categoryConfig['schema'] ?? 'CollectionPage',
            keywords: $keywords,
            author: $categoryConfig['author'] ?? $siteData['name'],
            robots: $categoryConfig['robots'] ?? 'index, follow',
            model: $model,
        );
    }

    private function extractSearchData($params, array $siteData): PageData
    {
        $searchConfig = $this->config['pages']['search'] ?? [];
        $defaults = $this->config['defaults'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];
        $query = htmlspecialchars_decode($params['query'] ?? '');

        $titleTemplate = $searchConfig['title'] ?? $fallbacks['search_title'] ?? 'Search results for: :query - :site';
        $title = str_replace([':query', ':site'], [$query, $siteData['name']], $titleTemplate);

        $descriptionTemplate = $searchConfig['description'] ?? $fallbacks['search_description'] ?? 'Find news and articles about: :query';
        $description = str_replace(':query', $query, $descriptionTemplate);

        $searchKeyword = $fallbacks['search_keyword'] ?? 'search';

        return new PageData(
            title: $title,
            description: $description,
            image: $siteData['logo'],
            schema: 'SearchResultsPage',
            keywords: array_merge([$searchKeyword, $query], $searchConfig['keywords'] ?? []),
            author: $searchConfig['author'] ?? $siteData['name'],
            robots: 'noindex, follow',
        );
    }

    private function getModelAttribute($model, array $attributes, $default = null)
    {
        if ($model === null) {
            return $default;
        }

        foreach ($attributes as $attribute) {
            if (is_array($model) && isset($model[$attribute])) {
                return $model[$attribute];
            }

            if (is_object($model) && (isset($model->$attribute) || method_exists($model, $attribute))) {
                return $model->$attribute ?? $model->$attribute();
            }
        }

        return $default;
    }

    private function limitWords(string $content, int $limit = 25): string
    {
        $text = strip_tags(htmlspecialchars_decode($content));
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        $excerpt = Str::words($text, $limit, '');
        
        if (mb_strlen($excerpt) > 160) {
            $excerpt = mb_substr($excerpt, 0, 157) . '...';
        }
        
        return $excerpt;
    }

    private function extractKeywords($model, array $defaults): array
    {
        $keywords = [];

        // Extract from tags
        if (is_object($model) && isset($model->tags) && method_exists($model->tags, 'pluck')) {
            $keywords = array_merge($keywords, $model->tags->pluck('name')->toArray());
        }

        // Extract from categories
        if (is_object($model) && isset($model->categories) && $model->categories->isNotEmpty()) {
            $keywords[] = $model->categories->first()->name;
        }

        // Add default keywords
        $keywords = array_merge($keywords, $defaults['keywords'] ?? []);

        return array_slice(array_unique($keywords), 0, 10);
    }

    private function extractCategoryKeywords($model, array $siteData, array $defaults): array
    {
        $keywords = [];
        $name = $this->getModelAttribute($model, ['name'], '');
        $fallbacks = $defaults['fallbacks'] ?? [];
        $prefix = $fallbacks['search_keyword_prefix'] ?? 'News ';

        if (!empty($name)) {
            $keywords[] = $name;
            $keywords[] = $prefix . $name;
        }

        $keywords[] = $siteData['name'];
        $keywords = array_merge($keywords, $defaults['keywords'] ?? []);

        return $keywords;
    }

    private function extractProductData($model, array $siteData): PageData
    {
        $defaults = $this->config['defaults'] ?? [];
        $productConfig = $this->config['pages']['product'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        $name = $this->getModelAttribute($model, ['name', 'title', 'product_name'], $fallbacks['product_name'] ?? 'Product');
        $name = htmlspecialchars_decode($name);
        
        $description = $this->limitWords(
            $this->getModelAttribute($model, ['description', 'content', 'product_description'], ''),
            $productConfig['description_limit'] ?? 30
        );
        $image = $this->getModelAttribute($model, ['image', 'photo', 'thumbnail', 'product_image'], null);
        
        $keywords = $this->extractProductKeywords($model, $defaults);

        return new PageData(
            title: ($productConfig['title_prefix'] ?? true) 
                ? $name . ' - ' . $siteData['name'] 
                : $name,
            description: $description,
            image: $image,
            schema: 'Product',
            keywords: $keywords,
            author: $siteData['name'],
            robots: $productConfig['robots'] ?? 'index, follow',
            model: $model,
        );
    }

    private function extractProductKeywords($model, array $defaults): array
    {
        $keywords = [];

        // Extract from product name
        $name = $this->getModelAttribute($model, ['name', 'title', 'product_name'], '');
        if (!empty($name)) {
            $keywords[] = $name;
        }

        // Extract from brand
        if (is_object($model) && isset($model->brand)) {
            $brand = $model->brand;
            if (is_object($brand) && isset($brand->name)) {
                $keywords[] = $brand->name;
            } elseif (is_string($brand)) {
                $keywords[] = $brand;
            }
        }

        // Extract from category
        if (is_object($model) && isset($model->category)) {
            $category = $model->category;
            if (is_object($category) && isset($category->name)) {
                $keywords[] = $category->name;
            } elseif (is_string($category)) {
                $keywords[] = $category;
            }
        }

        // Extract from tags
        if (is_object($model) && isset($model->tags) && method_exists($model->tags, 'pluck')) {
            $keywords = array_merge($keywords, $model->tags->pluck('name')->toArray());
        }

        // Add default keywords
        $keywords = array_merge($keywords, $defaults['keywords'] ?? []);

        return array_slice(array_unique($keywords), 0, 10);
    }

    private function getAuthor($model, array $siteData): string
    {
        if (!is_object($model)) {
            return $siteData['name'];
        }

        // List of possible relationship names
        $relations = ['writer', 'author', 'user', 'creator'];

        foreach ($relations as $relation) {
            if (isset($model->$relation) && is_object($model->$relation)) {
                $author = $model->$relation;
                return $author->name ?? $author->display_name ?? $siteData['name'];
            }
        }

        return $siteData['name'];
    }

    private function extractTagData($model, array $siteData): PageData
    {
        $defaults = $this->config['defaults'] ?? [];
        $tagConfig = $this->config['pages']['tag'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        $name = $this->getModelAttribute($model, ['name', 'title'], $fallbacks['tag_name'] ?? 'Tag');
        $name = htmlspecialchars_decode($name);
        
        $description = $this->getModelAttribute($model, ['description'], null);
        
        if (empty($description)) {
            $descriptionTemplate = $fallbacks['tag_description'] ?? 'Browse all articles tagged with :name';
            $description = str_replace(':name', $name, $descriptionTemplate);
        } else {
            $description = $this->limitWords($description, $tagConfig['description_limit'] ?? 30);
        }

        $pageSuffix = $this->getPageSuffix();
        $title = (($tagConfig['title_prefix'] ?? true) 
                ? $name . ' - ' . $siteData['name'] 
                : $name) . $pageSuffix;
        $description .= $pageSuffix;

        $image = $this->getModelAttribute($model, ['photo', 'image', 'thumbnail'], null);

        return new PageData(
            title: $title,
            description: $description,
            image: $image ?? $siteData['logo'],
            schema: $tagConfig['schema'] ?? 'CollectionPage',
            keywords: [$name, ...($defaults['keywords'] ?? [])],
            author: $siteData['name'],
            robots: $tagConfig['robots'] ?? 'index, follow',
        );
    }

    private function extractAuthorData($model, array $siteData): PageData
    {
        $defaults = $this->config['defaults'] ?? [];
        $authorConfig = $this->config['pages']['author'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        $name = $this->getModelAttribute($model, ['name', 'title', 'display_name'], $fallbacks['author_name'] ?? 'Author');
        $name = htmlspecialchars_decode($name);
        
        $description = $this->getModelAttribute($model, ['bio', 'description', 'about'], null);
        
        if (empty($description)) {
            $descriptionTemplate = $fallbacks['author_description'] ?? 'Articles written by :name';
            $description = str_replace(':name', $name, $descriptionTemplate);
        } else {
            $description = $this->limitWords($description, $authorConfig['description_limit'] ?? 30);
        }

        $pageSuffix = $this->getPageSuffix();
        $title = (($authorConfig['title_prefix'] ?? true) 
                ? $name . ' - ' . $siteData['name'] 
                : $name) . $pageSuffix;
        $description .= $pageSuffix;

        $image = $this->getModelAttribute($model, ['photo', 'avatar', 'image', 'profile_image'], null);

        return new PageData(
            title: $title,
            description: $description,
            image: $image ?? $siteData['logo'],
            schema: $authorConfig['schema'] ?? 'ProfilePage',
            keywords: [$name, ...($defaults['keywords'] ?? [])],
            author: $name,
            robots: $authorConfig['robots'] ?? 'index, follow',
        );
    }

    private function extractArchiveData($model, array $siteData): PageData
    {
        $defaults = $this->config['defaults'] ?? [];
        $archiveConfig = $this->config['pages']['archive'] ?? [];

        $title = is_array($model) ? ($model['title'] ?? 'Archive') : 'Archive';
        $description = is_array($model) ? ($model['description'] ?? null) : null;
        
        if (empty($description)) {
            $description = $archiveConfig['description'] ?? 'Browse our article archive';
        }

        $pageSuffix = $this->getPageSuffix();
        $title = ($title . ' - ' . $siteData['name']) . $pageSuffix;
        $description .= $pageSuffix;

        return new PageData(
            title: $title,
            description: $description,
            image: $siteData['logo'],
            schema: $archiveConfig['schema'] ?? 'CollectionPage',
            keywords: $defaults['keywords'] ?? [],
            author: $siteData['name'],
            robots: $archiveConfig['robots'] ?? 'index, follow',
        );
    }

    private function extractPageData($model, array $siteData): PageData
    {
        $defaults = $this->config['defaults'] ?? [];
        $pageConfig = $this->config['pages']['page'] ?? [];
        $fallbacks = $defaults['fallbacks'] ?? [];

        $title = $this->getModelAttribute($model, ['title', 'name'], $fallbacks['page_title'] ?? 'Page');
        $title = htmlspecialchars_decode($title);
        
        $description = $this->getModelAttribute($model, ['description', 'content', 'excerpt'], null);
        $description = $this->limitWords($description ?? '', $pageConfig['description_limit'] ?? 30);

        $image = $this->getModelAttribute($model, ['photo', 'image', 'featured_image'], null);

        return new PageData(
            title: ($pageConfig['title_prefix'] ?? true) 
                ? $title . ' - ' . $siteData['name'] 
                : $title,
            description: $description ?: $siteData['description'],
            image: $image ?? $siteData['logo'],
            schema: $pageConfig['schema'] ?? 'WebPage',
            keywords: $defaults['keywords'] ?? [],
            author: $siteData['name'],
            robots: $pageConfig['robots'] ?? 'index, follow',
        );
    }
}
