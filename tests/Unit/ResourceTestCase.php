<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClient;

/**
 * Base class for all resource tests.
 *
 * Provides a mocked HttpClient and helpers to assert
 * the correct HTTP method + URI were called.
 */
abstract class ResourceTestCase extends TestCase
{
    protected const ACCOUNT_ID = 1;
    protected const BASE       = '/api/v1/accounts/1';

    protected HttpClient&MockObject $http;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = $this->createMock(HttpClient::class);
    }

    /**
     * Configure the mock to expect a GET call and return $response.
     */
    protected function expectGet(string $uri, array $response, array $query = []): void
    {
        $this->http
            ->expects($this->once())
            ->method('get')
            ->with($uri, $query)
            ->willReturn($response);
    }

    /**
     * Configure the mock to expect a POST call and return $response.
     */
    protected function expectPost(string $uri, array $body, array $response): void
    {
        $this->http
            ->expects($this->once())
            ->method('post')
            ->with($uri, $body)
            ->willReturn($response);
    }

    /**
     * Configure the mock to expect a PUT call and return $response.
     */
    protected function expectPut(string $uri, array $body, array $response): void
    {
        $this->http
            ->expects($this->once())
            ->method('put')
            ->with($uri, $body)
            ->willReturn($response);
    }

    /**
     * Configure the mock to expect a PATCH call and return $response.
     */
    protected function expectPatch(string $uri, array $body, array $response): void
    {
        $this->http
            ->expects($this->once())
            ->method('patch')
            ->with($uri, $body)
            ->willReturn($response);
    }

    /**
     * Configure the mock to expect a DELETE call and return $response.
     */
    protected function expectDelete(string $uri, array $body, array $response): void
    {
        $this->http
            ->expects($this->once())
            ->method('delete')
            ->with($uri, $body)
            ->willReturn($response);
    }

    /**
     * Configure the mock to expect requestWithToken (Platform API).
     */
    protected function expectPlatformRequest(
        string $method,
        string $uri,
        string $token,
        array $response
    ): void {
        $this->http
            ->expects($this->once())
            ->method('requestWithToken')
            ->with($method, $uri, $token, $this->anything())
            ->willReturn($response);
    }
}
