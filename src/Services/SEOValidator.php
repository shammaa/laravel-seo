<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Services;

use Shammaa\LaravelSEO\Exceptions\InvalidSEOConfigException;
use Shammaa\LaravelSEO\Exceptions\InvalidSchemaException;

class SEOValidator
{
    /**
     * Validate SEO configuration.
     *
     * @param array $config The configuration array
     * @param bool $throwException Whether to throw exception on invalid config
     * @return bool
     * @throws InvalidSEOConfigException
     */
    public static function validateConfig(array $config, bool $throwException = true): bool
    {
        // Validate site name
        if (empty($config['site_name'])) {
            if ($throwException) {
                throw new InvalidSEOConfigException("'site_name' is required");
            }
            return false;
        }

        // Validate default locale
        if (isset($config['default_locale']) && !is_string($config['default_locale'])) {
            if ($throwException) {
                throw new InvalidSEOConfigException("'default_locale' must be a string");
            }
            return false;
        }

        // Validate supported locales
        if (isset($config['supported_locales']) && !is_array($config['supported_locales'])) {
            if ($throwException) {
                throw new InvalidSEOConfigException("'supported_locales' must be an array");
            }
            return false;
        }

        return true;
    }

    /**
     * Validate URL.
     *
     * @param string $url The URL to validate
     * @param bool $throwException Whether to throw exception on invalid URL
     * @return bool
     * @throws InvalidSEOConfigException
     */
    public static function validateUrl(string $url, bool $throwException = true): bool
    {
        if (empty($url)) {
            if ($throwException) {
                throw new InvalidSEOConfigException("URL cannot be empty");
            }
            return false;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL) && !str_starts_with($url, '/')) {
            if ($throwException) {
                throw new InvalidSEOConfigException("Invalid URL format: {$url}");
            }
            return false;
        }

        return true;
    }

    /**
     * Validate image URL.
     *
     * @param string|null $imageUrl The image URL to validate
     * @param bool $throwException Whether to throw exception on invalid URL
     * @return bool
     * @throws InvalidSEOConfigException
     */
    public static function validateImageUrl(?string $imageUrl, bool $throwException = true): bool
    {
        if ($imageUrl === null || $imageUrl === '') {
            return true; // Optional field
        }

        return self::validateUrl($imageUrl, $throwException);
    }

    /**
     * Validate rating value.
     *
     * @param float $rating The rating value
     * @param float $maxRating The maximum rating value
     * @param bool $throwException Whether to throw exception on invalid rating
     * @return bool
     * @throws InvalidSchemaException
     */
    public static function validateRating(float $rating, float $maxRating = 5.0, bool $throwException = true): bool
    {
        if ($rating < 0 || $rating > $maxRating) {
            if ($throwException) {
                throw new InvalidSchemaException("Rating must be between 0 and {$maxRating}, got {$rating}");
            }
            return false;
        }

        return true;
    }

    /**
     * Validate schema data.
     *
     * @param string $schemaType The schema type
     * @param array $data The schema data
     * @param bool $throwException Whether to throw exception on invalid data
     * @return bool
     * @throws InvalidSchemaException
     */
    public static function validateSchema(string $schemaType, array $data, bool $throwException = true): bool
    {
        $requiredFields = match ($schemaType) {
            'Product' => ['name'],
            'Article' => ['headline'],
            'Event' => ['name', 'startDate'],
            'Recipe' => ['name'],
            default => [],
        };

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                if ($throwException) {
                    throw new InvalidSchemaException("Required field '{$field}' is missing for {$schemaType} schema");
                }
                return false;
            }
        }

        return true;
    }
}

