<?php

declare(strict_types=1);

namespace Relayo\SDK\Tests\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Relayo\SDK\Config;
use Relayo\SDK\Http\HttpClient;
use Relayo\SDK\Resources\WhatsAppResource;

class WhatsAppResourceTest extends TestCase
{
    private WhatsAppResource $whatsappResource;
    private MockHandler $mockHandler;
    private HttpClient $httpClient;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $config = new Config('https://api.relayo.com.br');
        $this->httpClient = new HttpClient(
            $config,
            $client,
            new \GuzzleHttp\Psr7\HttpFactory(),
            new \GuzzleHttp\Psr7\HttpFactory(),
            new NullLogger()
        );

        $this->whatsappResource = new WhatsAppResource($this->httpClient);
    }

    public function testListWhatsAppInstances(): void
    {
        $responseData = [
            'data' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => '123e4567-e89b-12d3-a456-426614174000',
                        'phone_number' => '5511999999999',
                        'name' => 'WhatsApp Principal',
                        'status_label' => 'Conectado',
                        'status_color' => 'green'
                    ]
                ],
                'per_page' => 10,
                'total' => 1
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->whatsappResource->list();

        $this->assertEquals($responseData['data'], $result);
    }

    public function testCreateWhatsAppInstance(): void
    {
        $requestData = ['phone_number' => '5511999999999'];
        $responseData = [
            'data' => [
                'id' => '123e4567-e89b-12d3-a456-426614174000',
                'phone_number' => '5511999999999'
            ]
        ];

        $this->mockHandler->append(
            new Response(201, [], json_encode($responseData))
        );

        $result = $this->whatsappResource->create($requestData);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testGetWhatsAppInstance(): void
    {
        $instanceId = '123e4567-e89b-12d3-a456-426614174000';
        $responseData = [
            'data' => [
                'id' => $instanceId,
                'phone_number' => '5511999999999',
                'name' => 'WhatsApp Principal',
                'status_label' => 'Conectado',
                'status_color' => 'green'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->whatsappResource->get($instanceId);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testUpdateWhatsAppInstance(): void
    {
        $instanceId = '123e4567-e89b-12d3-a456-426614174000';
        $requestData = ['name' => 'WhatsApp Atualizado'];
        $responseData = [
            'data' => [
                'id' => $instanceId,
                'phone_number' => '5511999999999',
                'name' => 'WhatsApp Atualizado'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->whatsappResource->update($instanceId, $requestData);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testDeleteWhatsAppInstance(): void
    {
        $instanceId = '123e4567-e89b-12d3-a456-426614174000';

        $this->mockHandler->append(
            new Response(200, [], json_encode(['data' => []]))
        );

        // Não deve lançar exceção
        $this->whatsappResource->delete($instanceId);

        $this->assertTrue(true); // Teste passou se não lançou exceção
    }

    public function testFindByPhoneNumber(): void
    {
        $phoneNumber = '5511999999999';
        $responseData = [
            'data' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => '123e4567-e89b-12d3-a456-426614174000',
                        'phone_number' => $phoneNumber,
                        'name' => 'WhatsApp Principal'
                    ]
                ],
                'per_page' => 5,
                'total' => 1
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->whatsappResource->findByPhoneNumber($phoneNumber, 5);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testListPaginated(): void
    {
        $responseData = [
            'data' => [
                'current_page' => 2,
                'data' => [
                    [
                        'id' => '123e4567-e89b-12d3-a456-426614174000',
                        'phone_number' => '5511999999999'
                    ]
                ],
                'per_page' => 5,
                'total' => 10
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->whatsappResource->listPaginated(2, 5);

        $this->assertEquals($responseData['data'], $result);
    }
}
