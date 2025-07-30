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
use Relayo\SDK\Resources\ServerResource;

class ServerResourceTest extends TestCase
{
    private ServerResource $serverResource;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $config = new Config('https://api.relayo.com.br');
        $httpClient = new HttpClient(
            $config,
            $client,
            new \GuzzleHttp\Psr7\HttpFactory(),
            new \GuzzleHttp\Psr7\HttpFactory(),
            new NullLogger()
        );

        $this->serverResource = new ServerResource($httpClient);
    }

    public function testListServers(): void
    {
        $responseData = [
            'data' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => '123e4567-e89b-12d3-a456-426614174000',
                        'name' => 'Test Server',
                        'status' => 'active',
                        'ip_address' => '192.168.1.100'
                    ]
                ],
                'per_page' => 10,
                'total' => 1
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->serverResource->list();

        $this->assertEquals($responseData['data'], $result);
    }

    public function testCreateServer(): void
    {
        $serverData = [
            'name' => 'New Server',
            'ip_address' => '192.168.1.101',
            'description' => 'Test server'
        ];

        $responseData = [
            'data' => [
                'id' => '123e4567-e89b-12d3-a456-426614174000',
                'name' => 'New Server',
                'ip_address' => '192.168.1.101',
                'description' => 'Test server'
            ]
        ];

        $this->mockHandler->append(
            new Response(201, [], json_encode($responseData))
        );

        $result = $this->serverResource->create($serverData);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testGetServer(): void
    {
        $serverId = '123e4567-e89b-12d3-a456-426614174000';
        $responseData = [
            'data' => [
                'id' => $serverId,
                'name' => 'Test Server',
                'status' => 'active',
                'ip_address' => '192.168.1.100'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->serverResource->get($serverId);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testUpdateServer(): void
    {
        $serverId = '123e4567-e89b-12d3-a456-426614174000';
        $updateData = [
            'name' => 'Updated Server'
        ];

        $responseData = [
            'data' => [
                'id' => $serverId,
                'name' => 'Updated Server',
                'status' => 'active',
                'ip_address' => '192.168.1.100'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->serverResource->update($serverId, $updateData);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testDeleteServer(): void
    {
        $serverId = '123e4567-e89b-12d3-a456-426614174000';

        $this->mockHandler->append(
            new Response(200, [], json_encode(['data' => []]))
        );

        // Não deve lançar exceção
        $this->serverResource->delete($serverId);
        $this->assertTrue(true);
    }

    public function testGetServerStats(): void
    {
        $serverId = '123e4567-e89b-12d3-a456-426614174000';
        $responseData = [
            'data' => [
                'cpu_usage' => 45.5,
                'memory_usage' => 67.2,
                'disk_usage' => 23.1,
                'active_instances' => 3
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->serverResource->getStats($serverId);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testActivateServer(): void
    {
        $serverId = '123e4567-e89b-12d3-a456-426614174000';
        $responseData = [
            'data' => [
                'id' => $serverId,
                'status' => 'active'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->serverResource->activate($serverId);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testDeactivateServer(): void
    {
        $serverId = '123e4567-e89b-12d3-a456-426614174000';
        $responseData = [
            'data' => [
                'id' => $serverId,
                'status' => 'inactive'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->serverResource->deactivate($serverId);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testRestartServer(): void
    {
        $serverId = '123e4567-e89b-12d3-a456-426614174000';
        $responseData = [
            'data' => [
                'id' => $serverId,
                'status' => 'restarting'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->serverResource->restart($serverId);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testGetServerLogs(): void
    {
        $serverId = '123e4567-e89b-12d3-a456-426614174000';
        $responseData = [
            'data' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => 1,
                        'level' => 'info',
                        'message' => 'Server started successfully',
                        'timestamp' => '2024-12-19T10:00:00Z'
                    ]
                ],
                'per_page' => 10,
                'total' => 1
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->serverResource->getLogs($serverId);

        $this->assertEquals($responseData['data'], $result);
    }
} 