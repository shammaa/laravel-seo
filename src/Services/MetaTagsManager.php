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
        $this->metas[] = [
            'name' => $name,
            'content' => $content,
            'type' => $type,
        ];
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
            $html .= '<title>' . e($this->title) . '</title>' . PHP_EOL;
        }

        // Description
        if ($this->description) {
            $html .= '<meta name="description" content="' . e($this->description) . '">' . PHP_EOL;
        }

        // Canonical
        if ($this->canonical) {
            $html .= '<link rel="canonical" href="' . e($this->canonical) . '">' . PHP_EOL;
        }

        // Meta tags
        foreach ($this->metas as $meta) {
            $name = e($meta['name']);
            $content = e($meta['content']);
            $type = $meta['type'];

            if ($type === 'link') {
                $html .= '<link rel="' . $name . '" href="' . $content . '">' . PHP_EOL;
            } elseif ($type === 'http-equiv') {
                $html .= '<meta http-equiv="' . $name . '" content="' . $content . '">' . PHP_EOL;
            } elseif ($type === 'property') {
                $html .= '<meta property="' . $name . '" content="' . $content . '">' . PHP_EOL;
            } else {
                $html .= '<meta name="' . $name . '" content="' . $content . '">' . PHP_EOL;
            }
        }

        // Alternate languages
        foreach ($this->alternateLanguages as $alternate) {
            $html .= '<link rel="alternate" hreflang="' . e($alternate['locale']) . '" href="' . e($alternate['url']) . '">' . PHP_EOL;
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

