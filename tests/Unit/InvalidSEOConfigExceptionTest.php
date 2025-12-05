<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Shammaa\LaravelSEO\Exceptions\InvalidSEOConfigException;

class InvalidSEOConfigExceptionTest extends TestCase
{
    public function test_exception_message_without_config_key(): void
    {
        $exception = new InvalidSEOConfigException('Test error');
        $this->assertEquals('Test error', $exception->getMessage());
    }

    public function test_exception_message_with_config_key(): void
    {
        $exception = new InvalidSEOConfigException('Test error', 'site_name');
        $this->assertStringContainsString('site_name', $exception->getMessage());
        $this->assertStringContainsString('Test error', $exception->getMessage());
    }
}

