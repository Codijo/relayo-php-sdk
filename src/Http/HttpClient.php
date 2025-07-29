<?php

declare(strict_types=1);

namespace Relayo\SDK\Http;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Relayo\SDK\Config;
use Relayo\SDK\Exceptions\ApiException;
use Relayo\SDK\Exceptions\AuthenticationException;
use Relayo\SDK\Exceptions\RateLimitException;

/**
 * Cliente HTTP para comunicação com a API Relayo
 */
class HttpClient
{
    private ?string $token = null;

    public function __construct(
        private readonly Config $config,
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Faz uma requisição GET
     *
     * @param array<string, mixed> $query
     */
    public function get(string $endpoint, array $query = []): ResponseInterface
    {
        $url = $this->buildUrl($endpoint, $query);
        $request = $this->requestFactory->createRequest('GET', $url);

        return $this->sendRequest($request);
    }

    /**
     * Faz uma requisição POST
     *
     * @param array<string, mixed> $data
     */
    public function post(string $endpoint, array $data = []): ResponseInterface
    {
        $url = $this->buildUrl($endpoint);
        $body = $this->streamFactory->createStream((string) json_encode($data));

        $request = $this->requestFactory->createRequest('POST', $url)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body);

        return $this->sendRequest($request);
    }

    /**
     * Faz uma requisição PUT
     *
     * @param array<string, mixed> $data
     */
    public function put(string $endpoint, array $data = []): ResponseInterface
    {
        $url = $this->buildUrl($endpoint);
        $body = $this->streamFactory->createStream((string) json_encode($data));

        $request = $this->requestFactory->createRequest('PUT', $url)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($body);

        return $this->sendRequest($request);
    }

    /**
     * Faz uma requisição DELETE
     */
    public function delete(string $endpoint): ResponseInterface
    {
        $url = $this->buildUrl($endpoint);
        $request = $this->requestFactory->createRequest('DELETE', $url);

        return $this->sendRequest($request);
    }

    /**
     * Envia uma requisição com retry automático
     */
    private function sendRequest(RequestInterface $request): ResponseInterface
    {
        $request = $this->addHeaders($request);

        if ($this->config->shouldLogRequests()) {
            $this->logger->info('Enviando requisição', [
                'method' => $request->getMethod(),
                'uri' => (string) $request->getUri(),
                'headers' => $request->getHeaders()
            ]);
        }

        $attempt = 0;
        $maxAttempts = $this->config->getMaxRetries() + 1;

        while ($attempt < $maxAttempts) {
            try {
                $response = $this->httpClient->sendRequest($request);

                if ($this->config->shouldLogResponses()) {
                    $this->logger->info('Resposta recebida', [
                        'status_code' => $response->getStatusCode(),
                        'headers' => $response->getHeaders()
                    ]);
                }

                $this->handleResponse($response);
                return $response;
            } catch (\Exception $e) {
                $attempt++;

                if ($attempt >= $maxAttempts) {
                    throw $this->createApiException($e);
                }

                $this->logger->warning('Tentativa falhou, tentando novamente', [
                    'attempt' => $attempt,
                    'max_attempts' => $maxAttempts,
                    'error' => $e->getMessage()
                ]);

                $this->waitBeforeRetry($attempt);
            }
        }

        throw new ApiException('Número máximo de tentativas excedido');
    }

    /**
     * Adiciona headers padrão à requisição
     */
    private function addHeaders(RequestInterface $request): RequestInterface
    {
        $headers = [
            'Accept' => 'application/json',
            'User-Agent' => 'Codijo-Relayo-PHP-SDK/1.0'
        ];

        if ($this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }

    /**
     * Constrói a URL completa
     *
     * @param array<string, mixed> $query
     */
    private function buildUrl(string $endpoint, array $query = []): string
    {
        $url = $this->config->getBaseUrl() . '/' . ltrim($endpoint, '/');

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    /**
     * Aguarda antes de tentar novamente
     */
    private function waitBeforeRetry(int $attempt): void
    {
        $delay = $this->config->getRetryDelay();

        if ($this->config->useExponentialBackoff()) {
            $delay = $delay * (2 ** ($attempt - 1));
        }

        sleep($delay);
    }

    /**
     * Trata a resposta da API
     */
    private function handleResponse(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        if ($statusCode >= 400) {
            $this->handleErrorResponse($response);
        }
    }

    /**
     * Trata respostas de erro
     */
    private function handleErrorResponse(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        switch ($statusCode) {
            case 401:
                throw new AuthenticationException(
                    $data['errors'][0] ?? 'Não autorizado',
                    $statusCode
                );
            case 429:
                throw new RateLimitException(
                    'Rate limit excedido',
                    $statusCode
                );
            default:
                $message = $data['errors'][0] ?? $data['message'] ?? 'Erro da API';
                throw new ApiException($message, $statusCode);
        }
    }

    /**
     * Cria uma exceção da API baseada na exceção original
     */
    private function createApiException(\Exception $e): ApiException
    {
        if ($e instanceof ApiException) {
            return $e;
        }

        return new ApiException('Erro de comunicação: ' . $e->getMessage(), 0, $e);
    }

    /**
     * Define o token de autenticação
     */
    public function setToken(string $token): void
    {
        $this->token = $token === '' ? null : $token;
    }

    /**
     * Retorna o token atual
     */
    public function getToken(): ?string
    {
        return $this->token;
    }
}
