<?php

declare(strict_types=1);

namespace Relayo\SDK;

/**
 * Configuração do SDK Relayo
 */
class Config
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private readonly string $baseUrl,
        private readonly array $options = []
    ) {
        $this->validateBaseUrl();
    }

    /**
     * Retorna a URL base da API
     */
    public function getBaseUrl(): string
    {
        return rtrim($this->baseUrl, '/');
    }

    /**
     * Retorna o timeout das requisições
     */
    public function getTimeout(): int
    {
        return $this->options['timeout'] ?? 30;
    }

    /**
     * Retorna o número máximo de tentativas
     */
    public function getMaxRetries(): int
    {
        return $this->options['max_retries'] ?? 3;
    }

    /**
     * Retorna o delay entre tentativas (em segundos)
     */
    public function getRetryDelay(): int
    {
        return $this->options['retry_delay'] ?? 1;
    }

    /**
     * Retorna se deve usar backoff exponencial
     */
    public function useExponentialBackoff(): bool
    {
        return $this->options['exponential_backoff'] ?? true;
    }

    /**
     * Retorna se deve logar requisições
     */
    public function shouldLogRequests(): bool
    {
        return $this->options['log_requests'] ?? false;
    }

    /**
     * Retorna se deve logar respostas
     */
    public function shouldLogResponses(): bool
    {
        return $this->options['log_responses'] ?? false;
    }

    /**
     * Retorna uma opção específica
     */
    public function getOption(string $key, mixed $default = null): mixed
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Retorna todas as opções
     *
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Valida a URL base
     */
    private function validateBaseUrl(): void
    {
        if (!filter_var($this->baseUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('URL base inválida: ' . $this->baseUrl);
        }
    }
}
