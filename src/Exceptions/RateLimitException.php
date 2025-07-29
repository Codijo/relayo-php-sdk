<?php

declare(strict_types=1);

namespace Relayo\SDK\Exceptions;

/**
 * Exceção para rate limiting
 */
class RateLimitException extends ApiException
{
    public function __construct(
        string $message = 'Rate limit excedido',
        int $code = 429,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
