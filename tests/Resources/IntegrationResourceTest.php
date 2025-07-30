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
use Relayo\SDK\Resources\IntegrationResource;

class IntegrationResourceTest extends TestCase
{
    private IntegrationResource $integrationResource;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $config = new Config('https://api.relayo.com.br');
        $logger = new NullLogger();
        
        $httpClient = new HttpClient($config, $client, new \GuzzleHttp\Psr7\HttpFactory(), new \GuzzleHttp\Psr7\HttpFactory(), $logger);
        $this->integrationResource = new IntegrationResource($httpClient);
    }

    public function testListIntegrations(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    [
                        'id' => 'integration-1',
                        'name' => 'Integration Test',
                        'status' => 'active'
                    ]
                ]
            ]))
        );

        $result = $this->integrationResource->list();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('integration-1', $result[0]['id']);
    }

    public function testCreateIntegration(): void
    {
        $this->mockHandler->append(
            new Response(201, [], json_encode([
                'data' => [
                    'id' => 'new-integration',
                    'name' => 'New Integration'
                ]
            ]))
        );

        $result = $this->integrationResource->create([
            'name' => 'New Integration',
            'type' => 'webhook'
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('new-integration', $result['id']);
    }

    public function testGetIntegration(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    'id' => 'integration-1',
                    'name' => 'Integration Test'
                ]
            ]))
        );

        $result = $this->integrationResource->get('integration-1');

        $this->assertIsArray($result);
        $this->assertEquals('integration-1', $result['id']);
    }

    public function testUpdateIntegration(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    'id' => 'integration-1',
                    'name' => 'Updated Integration'
                ]
            ]))
        );

        $result = $this->integrationResource->update('integration-1', [
            'name' => 'Updated Integration'
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('Updated Integration', $result['name']);
    }

    public function testDeleteIntegration(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode(['data' => []]))
        );

        $this->integrationResource->delete('integration-1');
        $this->assertTrue(true); // Se chegou aqui, não houve exceção
    }

    public function testActivateIntegration(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    'id' => 'integration-1',
                    'status' => 'active'
                ]
            ]))
        );

        $result = $this->integrationResource->activate('integration-1');

        $this->assertIsArray($result);
        $this->assertEquals('active', $result['status']);
    }

    public function testDeactivateIntegration(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    'id' => 'integration-1',
                    'status' => 'inactive'
                ]
            ]))
        );

        $result = $this->integrationResource->deactivate('integration-1');

        $this->assertIsArray($result);
        $this->assertEquals('inactive', $result['status']);
    }

    public function testTestIntegration(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    'success' => true,
                    'message' => 'Integration test successful'
                ]
            ]))
        );

        $result = $this->integrationResource->test('integration-1');

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
    }

    public function testGetIntegrationLogs(): void
    {
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'data' => [
                    [
                        'id' => 'log-1',
                        'message' => 'Test log entry'
                    ]
                ]
            ]))
        );

        $result = $this->integrationResource->getLogs('integration-1');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('log-1', $result[0]['id']);
    }
} 