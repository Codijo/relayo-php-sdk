<?php

declare(strict_types=1);

namespace Relayo\SDK\Resources;

use Relayo\SDK\Http\HttpClient;

/**
 * Recurso para gerenciamento de servidores
 */
class ServerResource
{
    public function __construct(
        private readonly HttpClient $httpClient
    ) {
    }

    /**
     * Lista todos os servidores
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function list(array $filters = []): array
    {
        $response = $this->httpClient->get('panel/application/server', $filters);
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }

    /**
     * Cria um novo servidor
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('panel/application/server', $data);
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Obtém um servidor específico
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        $response = $this->httpClient->get("panel/application/server/{$id}");
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }

    /**
     * Atualiza um servidor
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $response = $this->httpClient->put("panel/application/server/{$id}", $data);
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Exclui um servidor
     */
    public function delete(string $id): void
    {
        $this->httpClient->delete("panel/application/server/{$id}");
    }

    /**
     * Lista servidores com paginação
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
     * Busca servidores por nome
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
     * Obtém estatísticas do servidor
     *
     * @return array<string, mixed>
     */
    public function getStats(string $id): array
    {
        $response = $this->httpClient->get("panel/application/server/{$id}/stats");
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }

    /**
     * Ativa um servidor
     *
     * @return array<string, mixed>
     */
    public function activate(string $id): array
    {
        $response = $this->httpClient->post("panel/application/server/{$id}/activate");
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Desativa um servidor
     *
     * @return array<string, mixed>
     */
    public function deactivate(string $id): array
    {
        $response = $this->httpClient->post("panel/application/server/{$id}/deactivate");
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Reinicia um servidor
     *
     * @return array<string, mixed>
     */
    public function restart(string $id): array
    {
        $response = $this->httpClient->post("panel/application/server/{$id}/restart");
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Obtém logs do servidor
     *
     * @return array<string, mixed>
     */
    public function getLogs(string $id, array $filters = []): array
    {
        $response = $this->httpClient->get("panel/application/server/{$id}/logs", $filters);
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }
} 