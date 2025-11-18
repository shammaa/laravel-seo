<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Schemas;

final class RecipeSchema
{
    public function __construct(
        private array $config = []
    ) {
    }

    public function build(array $data): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Recipe',
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
        ];

        if (!empty($data['image'])) {
            $images = is_array($data['image']) ? $data['image'] : [$data['image']];
            $schema['image'] = $images;
        }

        // Prep Time (ISO 8601 duration)
        if (!empty($data['prepTime'])) {
            $schema['prepTime'] = $data['prepTime'];
        }

        // Cook Time
        if (!empty($data['cookTime'])) {
            $schema['cookTime'] = $data['cookTime'];
        }

        // Total Time
        if (!empty($data['totalTime'])) {
            $schema['totalTime'] = $data['totalTime'];
        }

        // Recipe Yield
        if (!empty($data['recipeYield'])) {
            $schema['recipeYield'] = $data['recipeYield'];
        }

        // Recipe Category
        if (!empty($data['recipeCategory'])) {
            $schema['recipeCategory'] = $data['recipeCategory'];
        }

        // Recipe Cuisine
        if (!empty($data['recipeCuisine'])) {
            $schema['recipeCuisine'] = $data['recipeCuisine'];
        }

        // Recipe Ingredients
        if (!empty($data['recipeIngredient'])) {
            $ingredients = is_array($data['recipeIngredient']) 
                ? $data['recipeIngredient'] 
                : [$data['recipeIngredient']];
            $schema['recipeIngredient'] = $ingredients;
        }

        // Recipe Instructions
        if (!empty($data['recipeInstructions'])) {
            $instructions = $data['recipeInstructions'];
            
            if (is_array($instructions) && isset($instructions[0]) && is_string($instructions[0])) {
                // Array of strings
                $schema['recipeInstructions'] = array_map(function ($instruction, $index) {
                    return [
                        '@type' => 'HowToStep',
                        'position' => $index + 1,
                        'text' => $instruction,
                    ];
                }, $instructions, array_keys($instructions));
            } elseif (is_array($instructions)) {
                // Already formatted
                $schema['recipeInstructions'] = $instructions;
            }
        }

        // Author
        if (!empty($data['author'])) {
            $author = $data['author'];
            if (is_string($author)) {
                $schema['author'] = [
                    '@type' => 'Person',
                    'name' => $author,
                ];
            } elseif (is_array($author)) {
                $schema['author'] = array_merge(
                    ['@type' => 'Person'],
                    $author
                );
            }
        }

        // Date Published
        if (!empty($data['datePublished'])) {
            $schema['datePublished'] = $data['datePublished'];
        }

        // Nutrition Information
        if (!empty($data['nutrition'])) {
            $nutrition = $data['nutrition'];
            $schema['nutrition'] = [
                '@type' => 'NutritionInformation',
            ];

            if (isset($nutrition['calories'])) {
                $schema['nutrition']['calories'] = $nutrition['calories'];
            }

            if (isset($nutrition['fatContent'])) {
                $schema['nutrition']['fatContent'] = $nutrition['fatContent'];
            }

            if (isset($nutrition['saturatedFatContent'])) {
                $schema['nutrition']['saturatedFatContent'] = $nutrition['saturatedFatContent'];
            }

            if (isset($nutrition['cholesterolContent'])) {
                $schema['nutrition']['cholesterolContent'] = $nutrition['cholesterolContent'];
            }

            if (isset($nutrition['sodiumContent'])) {
                $schema['nutrition']['sodiumContent'] = $nutrition['sodiumContent'];
            }

            if (isset($nutrition['carbohydrateContent'])) {
                $schema['nutrition']['carbohydrateContent'] = $nutrition['carbohydrateContent'];
            }

            if (isset($nutrition['fiberContent'])) {
                $schema['nutrition']['fiberContent'] = $nutrition['fiberContent'];
            }

            if (isset($nutrition['sugarContent'])) {
                $schema['nutrition']['sugarContent'] = $nutrition['sugarContent'];
            }

            if (isset($nutrition['proteinContent'])) {
                $schema['nutrition']['proteinContent'] = $nutrition['proteinContent'];
            }
        }

        // Aggregate Rating
        if (!empty($data['aggregateRating'])) {
            $rating = $data['aggregateRating'];
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $rating['ratingValue'] ?? 0,
                'bestRating' => $rating['bestRating'] ?? 5.0,
                'worstRating' => $rating['worstRating'] ?? 1.0,
                'ratingCount' => $rating['ratingCount'] ?? 0,
            ];
        }

        return $schema;
    }
}

