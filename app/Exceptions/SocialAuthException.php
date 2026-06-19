<?php

namespace App\Exceptions;

use RuntimeException;

class SocialAuthException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $platform = '',
        public readonly string $type = 'auth_error',
        \Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}
