<?php

declare(strict_types=1);

namespace Relayo\SDK\Auth;

use Psr\Log\LoggerInterface;
use Relayo\SDK\Http\HttpClient;

/**
 * Gerenciador de autenticação
 */
class AuthManager
{
    public function __construct(
        private readonly HttpClient $httpClient,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Faz login na API
     *
     * @return array<string, mixed>
     */
    public function login(string $email, string $password): array
    {
        $this->logger->info('Tentando fazer login', ['email' => $email]);

        $response = $this->httpClient->post('panel/customer/login', [
            'email' => $email,
            'password' => $password
        ]);

        $data = json_decode((string) $response->getBody(), true);

        if (isset($data['success']['token'])) {
            $token = $data['success']['token'];
            $this->httpClient->setToken($token);

            $this->logger->info('Login realizado com sucesso', ['email' => $email]);

            return $data;
        }

        throw new \RuntimeException('Token não encontrado na resposta');
    }

    /**
     * Faz logout da API
     */
    public function logout(): void
    {
        if (!$this->httpClient->getToken()) {
            $this->logger->warning('Tentativa de logout sem token');
            return;
        }

        $this->logger->info('Fazendo logout');

        try {
            $this->httpClient->post('panel/customer/logout');
            $this->httpClient->setToken('');

            $this->logger->info('Logout realizado com sucesso');
        } catch (\Exception $e) {
            $this->logger->error('Erro ao fazer logout', ['error' => $e->getMessage()]);
            throw $e;
        }
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
}
