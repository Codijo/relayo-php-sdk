<?php

declare(strict_types=1);

namespace Relayo\SDK;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Relayo\SDK\Auth\AuthManager;
use Relayo\SDK\Http\HttpClient;
use Relayo\SDK\Resources\WhatsAppResource;

/**
 * Cliente principal do SDK Relayo
 */
class RelayoSDK
{
    private HttpClient $httpClient;
    private AuthManager $authManager;
    private LoggerInterface $logger;

    public function __construct(
        private readonly Config $config,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger ?? new NullLogger();

        // Usar Guzzle como padrão se não for fornecido
        if ($httpClient === null) {
            $httpClient = new \GuzzleHttp\Client();
        }
        if ($requestFactory === null) {
            $requestFactory = new \GuzzleHttp\Psr7\HttpFactory();
        }
        if ($streamFactory === null) {
            $streamFactory = new \GuzzleHttp\Psr7\HttpFactory();
        }

        $this->httpClient = new HttpClient(
            $config,
            $httpClient,
            $requestFactory,
            $streamFactory,
            $this->logger
        );
        $this->authManager = new AuthManager($this->httpClient, $this->logger);
    }

    /**
     * Cria uma instância do SDK com configuração padrão
     *
     * @param array<string, mixed> $options
     */
    public static function create(string $baseUrl, array $options = []): self
    {
        $config = new Config($baseUrl, $options);
        return new self($config);
    }

    /**
     * Retorna o gerenciador de autenticação
     */
    public function auth(): AuthManager
    {
        return $this->authManager;
    }

    /**
     * Retorna o recurso WhatsApp
     */
    public function whatsapp(): WhatsAppResource
    {
        return new WhatsAppResource($this->httpClient);
    }

    /**
     * Define o token de autenticação
     */
    public function setToken(string $token): void
    {
        $this->httpClient->setToken($token);
    }

    /**
     * Retorna o token atual
     */
    public function getToken(): ?string
    {
        return $this->httpClient->getToken();
    }

    /**
     * Verifica se está autenticado
     */
    public function isAuthenticated(): bool
    {
        return $this->httpClient->getToken() !== null;
    }

    /**
     * Retorna a configuração do SDK
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}
