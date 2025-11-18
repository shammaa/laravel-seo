<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class HowToSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(string $name, array $steps, ?string $description = null, ?string $image = null): array
    {
        if (empty($steps)) {
            return [];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $name,
        ];

        if ($description) {
            $schema['description'] = $description;
        }

        if ($image) {
            $schema['image'] = $image;
        }

        $howToSteps = [];
        foreach ($steps as $index => $step) {
            $stepData = [
                '@type' => 'HowToStep',
                'position' => $index + 1,
            ];

            if (is_string($step)) {
                $stepData['text'] = $step;
            } elseif (is_array($step)) {
                if (!empty($step['text'])) {
                    $stepData['text'] = $step['text'];
                }
                if (!empty($step['name'])) {
                    $stepData['name'] = $step['name'];
                }
                if (!empty($step['image'])) {
                    $stepData['image'] = $step['image'];
                }
                if (!empty($step['url'])) {
                    $stepData['url'] = $step['url'];
                }
            }

            $howToSteps[] = $stepData;
        }

        $schema['step'] = $howToSteps;

        return $schema;
    }
}

