<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Services;

final class JsonLdManager
{
    private array $schemas = [];

    public function setTitle(string $title): self
    {
        $this->ensureBasicSchema();
        $this->schemas[0]['headline'] = $title;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->ensureBasicSchema();
        $this->schemas[0]['description'] = $description;
        return $this;
    }

    public function setType(string $type): self
    {
        $this->ensureBasicSchema();
        $this->schemas[0]['@type'] = $type;
        return $this;
    }

    public function addImage(string $url): self
    {
        $this->ensureBasicSchema();
        if (!isset($this->schemas[0]['image'])) {
            $this->schemas[0]['image'] = $url;
        } else {
            if (!is_array($this->schemas[0]['image'])) {
                $this->schemas[0]['image'] = [$this->schemas[0]['image']];
            }
            // Check for duplicates
            if (!in_array($url, $this->schemas[0]['image'], true)) {
                $this->schemas[0]['image'][] = $url;
            }
        }
        return $this;
    }
    
    public function addProperty(string $key, $value): self
    {
        $this->ensureBasicSchema();
        $this->schemas[0][$key] = $value;
        return $this;
    }

    public function add(array $schema): self
    {
        $this->schemas[] = $schema;
        return $this;
    }

    public function generate(): string
    {
        $html = '';

        foreach ($this->schemas as $schema) {
            if (empty($schema)) {
                continue;
            }
            
            $html .= '<script type="application/ld+json">';
            $html .= json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            $html .= '</script>' . PHP_EOL;
        }

        return $html;
    }

    public function reset(): void
    {
        $this->schemas = [];
    }

    private function ensureBasicSchema(): void
    {
        if (empty($this->schemas)) {
            $this->schemas[] = [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
            ];
        }
    }
}

