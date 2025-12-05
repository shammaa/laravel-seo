<?php

declare(strict_types=1);

namespace Shammaa\LaravelSEO\Exceptions;

use InvalidArgumentException;

class InvalidSchemaException extends InvalidArgumentException
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message, ?string $schemaType = null)
    {
        if ($schemaType) {
            $message = "Invalid schema '{$schemaType}': {$message}";
        }
        
        parent::__construct($message);
    }
}

