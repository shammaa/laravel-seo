<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class FAQSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(array $faqs): array
    {
        if (empty($faqs)) {
            return [];
        }

        $mainEntity = [];

        foreach ($faqs as $faq) {
            if (empty($faq['question']) || empty($faq['answer'])) {
                continue;
            }

            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        if (empty($mainEntity)) {
            return [];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }
}

