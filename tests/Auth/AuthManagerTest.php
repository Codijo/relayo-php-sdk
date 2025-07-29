<?php

declare(strict_types=1);

namespace Relayo\SDK\Tests\Auth;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Relayo\SDK\Auth\AuthManager;
use Relayo\SDK\Config;
use Relayo\SDK\Http\HttpClient;

class AuthManagerTest extends TestCase
{
    private AuthManager $authManager;
    private HttpClient $httpClient;

    protected function setUp(): void
    {
        $config = new Config('https://api.relayo.com.br');
        $this->httpClient = new HttpClient(
            $config,
            new \GuzzleHttp\Client(),
            new \GuzzleHttp\Psr7\HttpFactory(),
            new \GuzzleHttp\Psr7\HttpFactory(),
            new NullLogger()
        );

        $this->authManager = new AuthManager($this->httpClient, new NullLogger());
    }

    public function testSetToken(): void
    {
        $token = 'test-token-123';
        $this->authManager->setToken($token);

        $this->assertEquals($token, $this->authManager->getToken());
        $this->assertTrue($this->authManager->isAuthenticated());
    }

    public function testClearToken(): void
    {
        // Primeiro define um token
        $this->authManager->setToken('test-token-123');
        $this->assertTrue($this->authManager->isAuthenticated());

        // Remove o token
        $this->authManager->clearToken();

        $this->assertNull($this->authManager->getToken());
        $this->assertFalse($this->authManager->isAuthenticated());
    }

    public function testValidateTokenWithValidToken(): void
    {
        $this->authManager->setToken('valid-token-123');

        $this->assertTrue($this->authManager->validateToken());
    }

    public function testValidateTokenWithEmptyToken(): void
    {
        $this->authManager->setToken('');

        $this->assertFalse($this->authManager->validateToken());
    }

    public function testValidateTokenWithWhitespaceToken(): void
    {
        $this->authManager->setToken('   ');

        $this->assertFalse($this->authManager->validateToken());
    }

    public function testValidateTokenWithNullToken(): void
    {
        $this->assertFalse($this->authManager->validateToken());
    }

    public function testIsAuthenticatedReturnsFalseWhenNoToken(): void
    {
        $this->assertFalse($this->authManager->isAuthenticated());
    }

    public function testIsAuthenticatedReturnsTrueWhenHasToken(): void
    {
        $this->authManager->setToken('test-token-123');

        $this->assertTrue($this->authManager->isAuthenticated());
    }

    public function testGetTokenReturnsNullWhenNoToken(): void
    {
        $this->assertNull($this->authManager->getToken());
    }

    public function testGetTokenReturnsTokenWhenSet(): void
    {
        $token = 'test-token-123';
        $this->authManager->setToken($token);

        $this->assertEquals($token, $this->authManager->getToken());
    }
}
