<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Platform\Resources;

use RamiroEstrella\ChatwootPhpSdk\Http\HttpClientInterface;

abstract class BasePlatformResource
{
    protected HttpClientInterface $http;
    protected string $platformToken;

    public function __construct(HttpClientInterface $http, string $platformToken)
    {
        $this->http          = $http;
        $this->platformToken = $platformToken;
    }

    /**
     * Platform API routes live under /platform/api/v1/ — NOT /api/v1/
     * This is a completely separate route namespace from the Application API.
     */
    protected function platformPath(string $suffix = ''): string
    {
        $path = '/platform/api/v1';

        if ($suffix !== '') {
            $path .= '/' . ltrim($suffix, '/');
        }

        return $path;
    }

    protected function httpGet(string $uri, array $query = []): array
    {
        return $this->http->requestWithToken('GET', $uri, $this->platformToken, [
            \GuzzleHttp\RequestOptions::QUERY => $query,
        ]);
    }

    protected function httpPost(string $uri, array $body = []): array
    {
        return $this->http->requestWithToken('POST', $uri, $this->platformToken, [
            \GuzzleHttp\RequestOptions::JSON => $body,
        ]);
    }

    protected function httpPatch(string $uri, array $body = []): array
    {
        return $this->http->requestWithToken('PATCH', $uri, $this->platformToken, [
            \GuzzleHttp\RequestOptions::JSON => $body,
        ]);
    }

    protected function httpDelete(string $uri, array $body = []): array
    {
        return $this->http->requestWithToken('DELETE', $uri, $this->platformToken, [
            \GuzzleHttp\RequestOptions::JSON => $body,
        ]);
    }

    protected function filterParams(array $params): array
    {
        return array_filter($params, fn ($value) => $value !== null);
    }
}
