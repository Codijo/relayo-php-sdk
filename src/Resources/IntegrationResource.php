<?php

declare(strict_types=1);

namespace Relayo\SDK\Resources;

use Relayo\SDK\Http\HttpClient;

/**
 * Recurso para gerenciamento de integrações
 */
class IntegrationResource
{
    public function __construct(
        private readonly HttpClient $httpClient
    ) {
    }

    /**
     * Lista todas as integrações
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function list(array $filters = []): array
    {
        $response = $this->httpClient->get('api/panel/application/integration/api', $filters);
        $data = json_decode((string) $response->getBody(), true);

        // Se a resposta tem estrutura de paginação, retorna apenas os dados
        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }

        // Se não tem estrutura de paginação, retorna a resposta completa
        return $data ?? [];
    }

    /**
     * Cria uma nova integração
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('api/panel/application/integration/api', $data);
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Obtém uma integração específica
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        $response = $this->httpClient->get("api/panel/application/integration/api/{$id}");
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }

    /**
     * Atualiza uma integração
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $response = $this->httpClient->put("api/panel/application/integration/api/{$id}", $data);
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Exclui uma integração
     */
    public function delete(string $id): void
    {
        $this->httpClient->delete("api/panel/application/integration/api/{$id}");
    }

    /**
     * Lista integrações com paginação
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
     * Busca integrações por nome
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
     * Ativa uma integração
     *
     * @return array<string, mixed>
     */
    public function activate(string $id): array
    {
        $response = $this->httpClient->post("api/panel/application/integration/{$id}/activate");
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Desativa uma integração
     *
     * @return array<string, mixed>
     */
    public function deactivate(string $id): array
    {
        $response = $this->httpClient->post("api/panel/application/integration/{$id}/deactivate");
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Testa uma integração
     *
     * @return array<string, mixed>
     */
    public function test(string $id): array
    {
        $response = $this->httpClient->post("api/panel/application/integration/{$id}/test");
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Obtém logs de uma integração
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getLogs(string $id, array $filters = []): array
    {
        $response = $this->httpClient->get("api/panel/application/integration/{$id}/logs", $filters);
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }
} 