<?php

declare(strict_types=1);

namespace Relayo\SDK\Exceptions;

/**
 * Exceção para erros de autenticação
 */
class AuthenticationException extends ApiException
{
    public function __construct(
        string $message = 'Erro de autenticação',
        int $code = 401,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
