<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Platform;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClient;

/**
 * Base for Platform resource tests.
 *
 * Platform resources route through requestWithToken() rather than the standard
 * get/post/patch/delete helpers, so we match on that method.
 */
abstract class PlatformResourceTestCase extends TestCase
{
    protected const TOKEN = 'platform_token_xyz';
    protected const BASE  = '/platform/api/v1';

    protected HttpClient&MockObject $http;

    protected function setUp(): void
    {
        parent::setUp();
        $this->http = $this->createMock(HttpClient::class);
    }

    /**
     * Expect a single requestWithToken call with the given method and URI.
     * We don't care about the options array internals — that's BasePlatformResource's job.
     */
    protected function expectPlatform(string $method, string $uri, array $response): void
    {
        $this->http
            ->expects($this->once())
            ->method('requestWithToken')
            ->with($method, $uri, self::TOKEN, $this->anything())
            ->willReturn($response);
    }
}
