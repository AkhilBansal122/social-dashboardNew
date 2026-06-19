<?php

namespace App\Exceptions;

use RuntimeException;

class SocialApiException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $platform = '',
        public readonly int $apiStatusCode = 0,
        public readonly string $type = 'api_error',
        \Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function isTokenError(): bool
    {
        return $this->apiStatusCode === 401
            || $this->apiStatusCode === 403
            || $this->type === 'token_expired'
            || str_contains(strtolower($this->getMessage()), 'token');
    }
}
