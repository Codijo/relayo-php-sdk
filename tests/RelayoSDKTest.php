<?php

declare(strict_types=1);

namespace Relayo\SDK\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Relayo\SDK\Config;
use Relayo\SDK\RelayoSDK;

class RelayoSDKTest extends TestCase
{
    private RelayoSDK $sdk;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);

        $config = new Config('https://api.relayo.com.br');
        $this->sdk = new RelayoSDK(
            $config,
            $httpClient,
            new \GuzzleHttp\Psr7\HttpFactory(),
            new \GuzzleHttp\Psr7\HttpFactory(),
            new NullLogger()
        );
    }

    public function testCreateSDKWithStaticMethod(): void
    {
        $sdk = RelayoSDK::create('https://api.relayo.com.br');

        $this->assertInstanceOf(RelayoSDK::class, $sdk);
        $this->assertEquals('https://api.relayo.com.br', $sdk->getConfig()->getBaseUrl());
    }

    public function testSetAndGetToken(): void
    {
        $token = 'test-token-123';

        $this->sdk->setToken($token);

        $this->assertEquals($token, $this->sdk->getToken());
        $this->assertTrue($this->sdk->isAuthenticated());
    }

    public function testIsAuthenticatedReturnsFalseWhenNoToken(): void
    {
        $this->assertFalse($this->sdk->isAuthenticated());
    }

    public function testGetAuthManager(): void
    {
        $authManager = $this->sdk->auth();

        $this->assertInstanceOf(\Relayo\SDK\Auth\AuthManager::class, $authManager);
    }

    public function testGetWhatsAppResource(): void
    {
        $whatsapp = $this->sdk->whatsapp();

        $this->assertInstanceOf(\Relayo\SDK\Resources\WhatsAppResource::class, $whatsapp);
    }

    public function testConfigValidation(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Config('invalid-url');
    }
}
