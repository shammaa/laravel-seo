<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Services;

final class OpenGraphManager
{
    private array $properties = [];
    private array $images = [];

    public function setTitle(string $title): self
    {
        return $this->addProperty('og:title', $title);
    }

    public function setDescription(string $description): self
    {
        return $this->addProperty('og:description', $description);
    }

    public function setUrl(string $url): self
    {
        return $this->addProperty('og:url', $url);
    }

    public function setType(string $type): self
    {
        return $this->addProperty('og:type', $type);
    }

    public function addProperty(string $property, string $value): self
    {
        $this->properties[$property] = $value;
        return $this;
    }

    public function addImage(string $url, ?int $width = null, ?int $height = null, ?string $type = null, ?string $alt = null): self
    {
        // Check for duplicate images by URL
        foreach ($this->images as $index => $existingImage) {
            if ($existingImage['url'] === $url) {
                // Update existing image
                if ($width !== null) {
                    $this->images[$index]['width'] = $width;
                }
                if ($height !== null) {
                    $this->images[$index]['height'] = $height;
                }
                if ($type !== null) {
                    $this->images[$index]['type'] = $type;
                }
                if ($alt !== null) {
                    $this->images[$index]['alt'] = $alt;
                }
                return $this;
            }
        }
        
        // Add new image
        $image = ['url' => $url];
        
        if ($width !== null) {
            $image['width'] = $width;
        }
        if ($height !== null) {
            $image['height'] = $height;
        }
        if ($type !== null) {
            $image['type'] = $type;
        }
        if ($alt !== null) {
            $image['alt'] = $alt;
        }
        
        $this->images[] = $image;
        return $this;
    }

    public function generate(): string
    {
        $html = '';

        // Properties
        foreach ($this->properties as $property => $value) {
            $html .= '<meta property="' . e($property) . '" content="' . e($value) . '">' . PHP_EOL;
        }

        // Images
        foreach ($this->images as $image) {
            $html .= '<meta property="og:image" content="' . e($image['url']) . '">' . PHP_EOL;
            
            if (isset($image['width'])) {
                $html .= '<meta property="og:image:width" content="' . e((string) $image['width']) . '">' . PHP_EOL;
            }
            if (isset($image['height'])) {
                $html .= '<meta property="og:image:height" content="' . e((string) $image['height']) . '">' . PHP_EOL;
            }
            if (isset($image['type'])) {
                $html .= '<meta property="og:image:type" content="' . e($image['type']) . '">' . PHP_EOL;
            }
            if (isset($image['alt'])) {
                $html .= '<meta property="og:image:alt" content="' . e($image['alt']) . '">' . PHP_EOL;
            }
        }

        return $html;
    }

    public function reset(): void
    {
        $this->properties = [];
        $this->images = [];
    }
}

