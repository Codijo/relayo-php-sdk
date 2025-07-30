<?php

declare(strict_types=1);

namespace Relayo\SDK\Resources;

use Relayo\SDK\Http\HttpClient;

/**
 * Recurso para gerenciamento de aplicações
 */
class ApplicationResource
{
    public function __construct(
        private readonly HttpClient $httpClient
    ) {
    }

    /**
     * Lista todas as aplicações
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function list(array $filters = []): array
    {
        $response = $this->httpClient->get('panel/application', $filters);
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }

    /**
     * Cria uma nova aplicação
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('panel/application', $data);
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Obtém uma aplicação específica
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        $response = $this->httpClient->get("panel/application/{$id}");
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }

    /**
     * Atualiza uma aplicação
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $response = $this->httpClient->put("panel/application/{$id}", $data);
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Exclui uma aplicação
     */
    public function delete(string $id): void
    {
        $this->httpClient->delete("panel/application/{$id}");
    }

    /**
     * Lista aplicações com paginação
     *
     * @return array<string, mixed>
     */
    public function listPaginated(int $page = 1, int $perPage = 10): array
    {
        return $this->list([
            'page' => $page,
            'per_page' => $perPage
        ]);
    }

    /**
     * Busca aplicações por nome
     *
     * @return array<string, mixed>
     */
    public function findByName(string $name, int $perPage = 10): array
    {
        return $this->list([
            'name' => $name,
            'per_page' => $perPage
        ]);
    }

    /**
     * Obtém estatísticas da aplicação
     *
     * @return array<string, mixed>
     */
    public function getStats(string $id): array
    {
        $response = $this->httpClient->get("panel/application/{$id}/stats");
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }

    /**
     * Ativa uma aplicação
     *
     * @return array<string, mixed>
     */
    public function activate(string $id): array
    {
        $response = $this->httpClient->post("panel/application/{$id}/activate");
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Desativa uma aplicação
     *
     * @return array<string, mixed>
     */
    public function deactivate(string $id): array
    {
        $response = $this->httpClient->post("panel/application/{$id}/deactivate");
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }
} 