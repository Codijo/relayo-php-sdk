<?php

declare(strict_types=1);

namespace Relayo\SDK\Resources;

use Relayo\SDK\Http\HttpClient;

/**
 * Recurso para gerenciamento de delivery WhatsApp
 */
class DeliveryWhatsAppResource
{
    public function __construct(
        private readonly HttpClient $httpClient
    ) {
    }

    /**
     * Lista todas as configurações de delivery WhatsApp
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function list(array $filters = []): array
    {
        $response = $this->httpClient->get('api/panel/application/delivery/whatsapp/queue/api/delivery', $filters);
        $data = json_decode((string) $response->getBody(), true);

        // Se a resposta tem estrutura de paginação, retorna apenas os dados
        if (isset($data['data']) && is_array($data['data'])) {
            return $data['data'];
        }

        // Se não tem estrutura de paginação, retorna a resposta completa
        return $data ?? [];
    }

    /**
     * Obtém histórico de delivery WhatsApp
     *
     * @return array<string, mixed>
     */
    public function getHistory(array $filters = []): array
    {
        $response = $this->httpClient->get('api/panel/application/delivery/whatsapp/history', $filters);
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }

    /**
     * Obtém um item específico do histórico
     *
     * @return array<string, mixed>
     */
    public function getHistoryItem(string $id): array
    {
        $response = $this->httpClient->get("api/panel/application/delivery/whatsapp/history/{$id}");
        $data = json_decode((string) $response->getBody(), true);

        return $data['data'] ?? [];
    }





    /**
     * Envia mensagem de texto via WhatsApp
     *
     * @param string $instanceId ID da instância WhatsApp
     * @param string $to Número de telefone de destino (formato: 555199693860)
     * @param string $message Mensagem a ser enviada
     * @return array<string, mixed>
     */
    public function sendTextMessage(string $instanceId, string $to, string $message): array
    {
        $data = [
            'instance_id' => $instanceId,
            'to' => $to,
            'message' => $message
        ];

        $response = $this->httpClient->post('api/panel/application/delivery/whatsapp/queue/api/delivery/text', $data);
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }

    /**
     * Envia mensagem de texto via WhatsApp com dados customizados
     *
     * @param array<string, mixed> $data Dados da mensagem (instance_id, to, message)
     * @return array<string, mixed>
     */
    public function sendTextMessageWithData(array $data): array
    {
        $response = $this->httpClient->post('api/panel/application/delivery/whatsapp/queue/api/delivery/text', $data);
        $responseData = json_decode((string) $response->getBody(), true);

        return $responseData['data'] ?? [];
    }
} 