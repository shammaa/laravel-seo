<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Services;

final class MetaTagsManager
{
    private array $metas = [];
    private ?string $title = null;
    private ?string $description = null;
    private ?string $canonical = null;
    private array $alternateLanguages = [];

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setCanonical(string $url): self
    {
        $this->canonical = $url;
        return $this;
    }

    public function addMeta(string $name, string $content, string $type = 'name'): self
    {
        // Some meta tags can have multiple values (like article:tag)
        $allowMultiple = in_array($name, ['article:tag', 'article:section', 'og:image']);
        
        if ($allowMultiple) {
            // Allow multiple values for these tags
            $this->metas[] = [
                'name' => $name,
                'content' => $content,
                'type' => $type,
            ];
        } else {
            // Check for duplicates - if same name and type exists, replace it
            $found = false;
            foreach ($this->metas as $index => $meta) {
                if ($meta['name'] === $name && $meta['type'] === $type) {
                    $this->metas[$index]['content'] = $content;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $this->metas[] = [
                    'name' => $name,
                    'content' => $content,
                    'type' => $type,
                ];
            }
        }
        
        return $this;
    }

    public function addAlternateLanguage(string $locale, string $url): self
    {
        $this->alternateLanguages[] = [
            'locale' => $locale,
            'url' => $url,
        ];
        return $this;
    }

    public function generate(): string
    {
        $html = '';

        // Title
        if ($this->title) {
            // For the title tag, we decode entities to show literal characters like " in source
            // We use strip_tags for safety to ensure no HTML breaks the title
            $title = htmlspecialchars_decode(strip_tags($this->title));
            $html .= '<title>' . $title . '</title>' . PHP_EOL;
        }

        // Description
        if ($this->description) {
            $description = e(htmlspecialchars_decode($this->description), false);
            $html .= '<meta name="description" content="' . $description . '">' . PHP_EOL;
        }

        // Canonical
        if ($this->canonical) {
            $html .= '<link rel="canonical" href="' . e($this->canonical, false) . '">' . PHP_EOL;
        }

        // Meta tags
        foreach ($this->metas as $meta) {
            $name = e($meta['name'], false);
            $content = e($meta['content'], false);
            $type = $meta['type'];

            if ($type === 'link') {
                $html .= '<link rel="' . $name . '" href="' . $content . '">' . PHP_EOL;
            } elseif ($type === 'http-equiv') {
                $html .= '<meta http-equiv="' . $name . '" content="' . $content . '">' . PHP_EOL;
            } elseif ($type === 'property') {
                $content = e(htmlspecialchars_decode((string)$meta['content']), false);
                $html .= '<meta property="' . $name . '" content="' . $content . '">' . PHP_EOL;
            } else {
                $content = e(htmlspecialchars_decode((string)$meta['content']), false);
                $html .= '<meta name="' . $name . '" content="' . $content . '">' . PHP_EOL;
            }
        }

        // Alternate languages
        foreach ($this->alternateLanguages as $alternate) {
            $html .= '<link rel="alternate" hreflang="' . e($alternate['locale'], false) . '" href="' . e($alternate['url'], false) . '">' . PHP_EOL;
        }

        return $html;
    }

    public function reset(): void
    {
        $this->metas = [];
        $this->title = null;
        $this->description = null;
        $this->canonical = null;
        $this->alternateLanguages = [];
    }
}

