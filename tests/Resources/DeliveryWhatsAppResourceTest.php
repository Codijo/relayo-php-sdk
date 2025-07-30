<?php

declare(strict_types=1);

namespace Relayo\SDK\Tests\Resources;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Relayo\SDK\Http\HttpClient;
use Relayo\SDK\Resources\DeliveryWhatsAppResource;
use Relayo\SDK\Config;
use Psr\Log\NullLogger;

class DeliveryWhatsAppResourceTest extends TestCase
{
    private DeliveryWhatsAppResource $deliveryWhatsAppResource;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        
        $config = new Config('https://api.relayo.com.br', [
            'timeout' => 30,
            'max_retries' => 3
        ]);
        
        $httpClient = new HttpClient(
            $config,
            new \GuzzleHttp\Client(['handler' => $handlerStack]),
            new \GuzzleHttp\Psr7\HttpFactory(),
            new \GuzzleHttp\Psr7\HttpFactory(),
            new NullLogger()
        );
        
        $this->deliveryWhatsAppResource = new DeliveryWhatsAppResource($httpClient);
    }

    public function testListDeliveryConfigurations(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    [
                        'id' => 'delivery-1',
                        'name' => 'Delivery Config Test',
                        'status' => 'active'
                    ]
                ]
            ]))
        );

        $result = $this->deliveryWhatsAppResource->list();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('delivery-1', $result[0]['id']);
    }

    public function testGetHistory(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    [
                        'id' => 'history-1',
                        'status' => 'sent',
                        'created_at' => '2024-01-01 10:00:00'
                    ]
                ]
            ]))
        );

        $result = $this->deliveryWhatsAppResource->getHistory();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('history-1', $result[0]['id']);
    }

    public function testGetHistoryItem(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    'id' => 'history-1',
                    'status' => 'sent',
                    'created_at' => '2024-01-01 10:00:00'
                ]
            ]))
        );

        $result = $this->deliveryWhatsAppResource->getHistoryItem('history-1');

        $this->assertIsArray($result);
        $this->assertEquals('history-1', $result['id']);
        $this->assertEquals('sent', $result['status']);
    }

    public function testSendTextMessage(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    'success' => true,
                    'message_id' => 'msg-456',
                    'status' => 'queued'
                ]
            ]))
        );

        $result = $this->deliveryWhatsAppResource->sendTextMessage(
            'inst_68BKRjIyM7LLsvfHAlq98TpscnnXTr6fK5mcHN11',
            '555199693860',
            'Aqui, iPORTO DEV!!!! |o|'
        );

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('msg-456', $result['message_id']);
        $this->assertEquals('queued', $result['status']);
    }

    public function testSendTextMessageWithData(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    'success' => true,
                    'message_id' => 'msg-789',
                    'status' => 'queued'
                ]
            ]))
        );

        $data = [
            'instance_id' => 'inst_68BKRjIyM7LLsvfHAlq98TpscnnXTr6fK5mcHN11',
            'to' => '555199693860',
            'message' => 'Teste via SDK'
        ];

        $result = $this->deliveryWhatsAppResource->sendTextMessageWithData($data);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals('msg-789', $result['message_id']);
        $this->assertEquals('queued', $result['status']);
    }
} 