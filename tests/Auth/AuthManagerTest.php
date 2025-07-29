<?php

declare(strict_types=1);

namespace Relayo\SDK\Tests\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Relayo\SDK\Auth\AuthManager;
use Relayo\SDK\Config;
use Relayo\SDK\Http\HttpClient;

class AuthManagerTest extends TestCase
{
    private AuthManager $authManager;
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

        $this->authManager = new AuthManager($this->httpClient, new NullLogger());
    }

    public function testSuccessfulLogin(): void
    {
        $responseData = [
            'success' => [
                'token' => 'test-token-123'
            ],
            'data' => [
                'id' => 1,
                'email' => 'test@example.com',
                'customer_id' => 123,
                'api_token' => 'api-token-456'
            ]
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $result = $this->authManager->login('test@example.com', 'password123');

        $this->assertEquals($responseData, $result);
        $this->assertEquals('test-token-123', $this->authManager->getToken());
        $this->assertTrue($this->authManager->isAuthenticated());
    }

    public function testLoginWithoutTokenInResponse(): void
    {
        $responseData = [
            'success' => [],
            'data' => []
        ];

        $this->mockHandler->append(
            new Response(200, [], json_encode($responseData))
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Token não encontrado na resposta');

        $this->authManager->login('test@example.com', 'password123');
    }

    public function testSuccessfulLogout(): void
    {
        // Primeiro faz login para ter um token
        $this->httpClient->setToken('test-token-123');

        $this->mockHandler->append(
            new Response(200, [], json_encode(['data' => []]))
        );

        $this->authManager->logout();

        $this->assertNull($this->authManager->getToken());
        $this->assertFalse($this->authManager->isAuthenticated());
    }

    public function testLogoutWithoutToken(): void
    {
        // Não deve lançar exceção
        $this->authManager->logout();

        $this->assertNull($this->authManager->getToken());
        $this->assertFalse($this->authManager->isAuthenticated());
    }

    public function testIsAuthenticatedReturnsFalseWhenNoToken(): void
    {
        $this->assertFalse($this->authManager->isAuthenticated());
    }

    public function testIsAuthenticatedReturnsTrueWhenHasToken(): void
    {
        $this->httpClient->setToken('test-token-123');

        $this->assertTrue($this->authManager->isAuthenticated());
    }
}
