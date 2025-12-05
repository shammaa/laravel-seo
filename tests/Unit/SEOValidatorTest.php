<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Shammaa\LaravelSEO\Exceptions\InvalidSEOConfigException;
use Shammaa\LaravelSEO\Exceptions\InvalidSchemaException;
use Shammaa\LaravelSEO\Services\SEOValidator;

class SEOValidatorTest extends TestCase
{
    public function test_validate_config_success(): void
    {
        $config = [
            'site_name' => 'Test Site',
            'default_locale' => 'en',
            'supported_locales' => ['en', 'ar'],
        ];

        $this->assertTrue(SEOValidator::validateConfig($config));
    }

    public function test_validate_config_throws_exception_when_site_name_missing(): void
    {
        $this->expectException(InvalidSEOConfigException::class);
        SEOValidator::validateConfig([]);
    }

    public function test_validate_url_success(): void
    {
        $this->assertTrue(SEOValidator::validateUrl('https://example.com'));
        $this->assertTrue(SEOValidator::validateUrl('/path/to/page'));
    }

    public function test_validate_url_throws_exception_when_invalid(): void
    {
        $this->expectException(InvalidSEOConfigException::class);
        SEOValidator::validateUrl('not a valid url');
    }

    public function test_validate_rating_success(): void
    {
        $this->assertTrue(SEOValidator::validateRating(4.5, 5.0));
        $this->assertTrue(SEOValidator::validateRating(0.0, 5.0));
        $this->assertTrue(SEOValidator::validateRating(5.0, 5.0));
    }

    public function test_validate_rating_throws_exception_when_invalid(): void
    {
        $this->expectException(InvalidSchemaException::class);
        SEOValidator::validateRating(6.0, 5.0);
    }

    public function test_validate_schema_success(): void
    {
        $data = [
            'name' => 'Test Product',
        ];

        $this->assertTrue(SEOValidator::validateSchema('Product', $data));
    }

    public function test_validate_schema_throws_exception_when_required_field_missing(): void
    {
        $this->expectException(InvalidSchemaException::class);
        SEOValidator::validateSchema('Product', []);
    }
}

