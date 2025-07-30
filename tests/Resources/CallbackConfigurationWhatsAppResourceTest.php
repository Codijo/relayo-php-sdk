<?php

declare(strict_types=1);

namespace Relayo\SDK\Tests\Resources;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Relayo\SDK\Http\HttpClient;
use Relayo\SDK\Resources\CallbackConfigurationWhatsAppResource;
use Relayo\SDK\Config;
use Psr\Log\NullLogger;

class CallbackConfigurationWhatsAppResourceTest extends TestCase
{
    private CallbackConfigurationWhatsAppResource $callbackConfigurationWhatsAppResource;
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
        
        $this->callbackConfigurationWhatsAppResource = new CallbackConfigurationWhatsAppResource($httpClient);
    }

    public function testGetCallbackConfiguration(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    'id' => '1',
                    'name' => 'Callback Test',
                    'url' => 'https://example.com/webhook',
                    'active' => true
                ]
            ]))
        );

        $result = $this->callbackConfigurationWhatsAppResource->get();

        $this->assertIsArray($result);
        $this->assertEquals('1', $result['id']);
        $this->assertEquals('Callback Test', $result['name']);
        $this->assertEquals('https://example.com/webhook', $result['url']);
    }

    public function testGetCallbackConfigurationById(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    'id' => 'callback-1',
                    'name' => 'Callback Config Test'
                ]
            ]))
        );

        $result = $this->callbackConfigurationWhatsAppResource->getById('callback-1');

        $this->assertIsArray($result);
        $this->assertEquals('callback-1', $result['id']);
    }

    public function testUpdateCallbackConfiguration(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    'id' => 'callback-1',
                    'name' => 'Updated Callback Config'
                ]
            ]))
        );

        $result = $this->callbackConfigurationWhatsAppResource->update('callback-1', [
            'name' => 'Updated Callback Config'
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('Updated Callback Config', $result['name']);
    }
} 