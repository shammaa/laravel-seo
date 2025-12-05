<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Exceptions;

use InvalidArgumentException;

class InvalidSEOConfigException extends InvalidArgumentException
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message, ?string $configKey = null)
    {
        if ($configKey) {
            $message = "Invalid SEO config for '{$configKey}': {$message}";
        }
        
        parent::__construct($message);
    }
}

