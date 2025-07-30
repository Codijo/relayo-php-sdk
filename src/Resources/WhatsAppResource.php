<?php

declare(strict_types=1);

namespace Relayo\SDK\Resources;

use Relayo\SDK\Http\HttpClient;

/**
 * Recurso para gerenciamento de instâncias WhatsApp
 */
class WhatsAppResource
{
    public function __construct(
        private readonly HttpClient $httpClient
    ) {
    }

    /**
     * Lista todas as instâncias WhatsApp
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function list(array $filters = []): array
    {
        $response = $this->httpClient->get('api/panel/application/server/instance/whatsapp', $filters);
        $data = json_decode((string) $response->getBody(), true);

        // Se a resposta tem estrutura de paginação, retorna apenas os dados
        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }

        // Se não tem estrutura de paginação, retorna a resposta completa
        return $data ?? [];
    }

    /**
     * Cria uma nova instância WhatsApp
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $response = $this->httpClient->post('api/panel/application/server/instance/whatsapp', $data);
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Obtém uma instância WhatsApp específica
     *
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        $response = $this->httpClient->get("api/panel/application/server/instance/whatsapp/{$id}");
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }

    /**
     * Atualiza uma instância WhatsApp
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $response = $this->httpClient->put("api/panel/application/server/instance/whatsapp/{$id}", $data);
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Exclui uma instância WhatsApp
     */
    public function delete(string $id): void
    {
        $this->httpClient->delete("api/panel/application/server/instance/whatsapp/{$id}");
    }

    /**
     * Busca instâncias por número de telefone
     *
     * @return array<string, mixed>
     */
    public function findByPhoneNumber(string $phoneNumber, int $perPage = 10): array
    {
        return $this->list([
            'phone_number' => $phoneNumber,
            'per_page' => $perPage
        ]);
    }

    /**
     * Lista instâncias com paginação
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
}
