<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Services;

final class TwitterCardManager
{
    private array $values = [];

    public function setType(string $type): self
    {
        return $this->addValue('card', $type);
    }

    public function setTitle(string $title): self
    {
        return $this->addValue('title', $title);
    }

    public function setDescription(string $description): self
    {
        return $this->addValue('description', $description);
    }

    public function setImage(string $url): self
    {
        return $this->addValue('image', $url);
    }

    public function addValue(string $key, string $value): self
    {
        $this->values[$key] = $value;
        return $this;
    }

    public function generate(): string
    {
        $html = '';

        foreach ($this->values as $key => $value) {
            $content = e(htmlspecialchars_decode((string)$value), false);
            $html .= '<meta name="twitter:' . e($key, false) . '" content="' . $content . '">' . PHP_EOL;
        }

        return $html;
    }

    public function reset(): void
    {
        $this->values = [];
    }
}

