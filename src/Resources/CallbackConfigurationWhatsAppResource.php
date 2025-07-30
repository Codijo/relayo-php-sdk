<?php

declare(strict_types=1);

namespace Relayo\SDK\Resources;

use Relayo\SDK\Http\HttpClient;

/**
 * Recurso para gerenciamento de configuração de callbacks WhatsApp
 */
class CallbackConfigurationWhatsAppResource
{
    public function __construct(
        private readonly HttpClient $httpClient
    ) {
    }

    /**
     * Lista todas as configurações de callback WhatsApp
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function get(): array
    {
        $response = $this->httpClient->get('api/panel/application/callback/configuration/whatsapp');
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }



    /**
     * Obtém uma configuração específica de callback WhatsApp
     *
     * @return array<string, mixed>
     */
    public function getById(string $id): array
    {
        $response = $this->httpClient->get("api/panel/application/callback/configuration/whatsapp/{$id}");
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }

    /**
     * Atualiza uma configuração de callback WhatsApp
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function update(string $id, array $data): array
    {
        $response = $this->httpClient->put("api/panel/application/callback/configuration/whatsapp/{$id}", $data);
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }




} 