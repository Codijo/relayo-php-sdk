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
use Relayo\SDK\Resources\ApplicationResource;

class ApplicationResourceTest extends TestCase
{
    private ApplicationResource $applicationResource;
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

        $this->applicationResource = new ApplicationResource($httpClient);
    }

    public function testListApplications(): void
    {
        $responseData = [
            'data' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => '123e4567-e89b-12d3-a456-426614174000',
                        'name' => 'Test Application',
                        'status' => 'active'
                    ]
                ],
                'per_page' => 10,
                'total' => 1
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->applicationResource->list();

        $this->assertEquals($responseData['data'], $result);
    }

    public function testCreateApplication(): void
    {
        $applicationData = [
            'name' => 'New Application',
            'description' => 'Test application'
        ];

        $responseData = [
            'data' => [
                'id' => '123e4567-e89b-12d3-a456-426614174000',
                'name' => 'New Application',
                'description' => 'Test application'
            ]
        ];

        $this->mockHandler->append(
            new Response(201, [], json_encode($responseData))
        );

        $result = $this->applicationResource->create($applicationData);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testGetApplication(): void
    {
        $applicationId = '123e4567-e89b-12d3-a456-426614174000';
        $responseData = [
            'data' => [
                'id' => $applicationId,
                'name' => 'Test Application',
                'status' => 'active'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->applicationResource->get($applicationId);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testUpdateApplication(): void
    {
        $applicationId = '123e4567-e89b-12d3-a456-426614174000';
        $updateData = [
            'name' => 'Updated Application'
        ];

        $responseData = [
            'data' => [
                'id' => $applicationId,
                'name' => 'Updated Application',
                'status' => 'active'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->applicationResource->update($applicationId, $updateData);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testDeleteApplication(): void
    {
        $applicationId = '123e4567-e89b-12d3-a456-426614174000';

        $this->mockHandler->append(
            new Response(200, [], json_encode(['data' => []]))
        );

        // Não deve lançar exceção
        $this->applicationResource->delete($applicationId);
        $this->assertTrue(true);
    }

    public function testGetApplicationStats(): void
    {
        $applicationId = '123e4567-e89b-12d3-a456-426614174000';
        $responseData = [
            'data' => [
                'total_instances' => 5,
                'active_instances' => 3,
                'inactive_instances' => 2
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->applicationResource->getStats($applicationId);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testActivateApplication(): void
    {
        $applicationId = '123e4567-e89b-12d3-a456-426614174000';
        $responseData = [
            'data' => [
                'id' => $applicationId,
                'status' => 'active'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->applicationResource->activate($applicationId);

        $this->assertEquals($responseData['data'], $result);
    }

    public function testDeactivateApplication(): void
    {
        $applicationId = '123e4567-e89b-12d3-a456-426614174000';
        $responseData = [
            'data' => [
                'id' => $applicationId,
                'status' => 'inactive'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->applicationResource->deactivate($applicationId);

        $this->assertEquals($responseData['data'], $result);
    }
} 