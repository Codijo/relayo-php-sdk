<?php

declare(strict_types=1);

namespace Relayo\SDK\Auth;

use Psr\Log\LoggerInterface;
use Relayo\SDK\Http\HttpClient;

/**
 * Gerenciador de autenticação via token Bearer
 */
class AuthManager
{
    public function __construct(
        private readonly HttpClient $httpClient,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Define o token de autenticação Bearer
     */
    public function setToken(string $token): void
    {
        $this->httpClient->setToken($token);
        $this->logger->info('Token de autenticação definido');
    }

    /**
     * Remove o token de autenticação
     */
    public function clearToken(): void
    {
        $this->httpClient->setToken('');
        $this->logger->info('Token de autenticação removido');
    }

    /**
     * Verifica se está autenticado
     */
    public function isAuthenticated(): bool
    {
        return $this->httpClient->getToken() !== null;
    }

    /**
     * Retorna o token atual
     */
    public function getToken(): ?string
    {
        return $this->httpClient->getToken();
    }

    /**
     * Valida se o token está presente e não está vazio
     */
    public function validateToken(): bool
    {
        $token = $this->getToken();
        return $token !== null && trim($token) !== '';
    }
}
