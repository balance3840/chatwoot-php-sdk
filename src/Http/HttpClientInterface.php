<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Http;

interface HttpClientInterface
{
    public function get(string $uri, array $query = [], array $headers = []): array;

    public function post(string $uri, array $body = [], array $headers = []): array;

    public function postMultipart(string $uri, array $multipart, array $headers = []): array;

    public function put(string $uri, array $body = [], array $headers = []): array;

    public function patch(string $uri, array $body = [], array $headers = []): array;

    public function delete(string $uri, array $body = [], array $headers = []): array;

    /**
     * Execute a request with an explicit token, bypassing the default credential.
     * Used by the Platform API which requires a separate token per request.
     */
    public function requestWithToken(string $method, string $uri, string $token, array $options = []): array;

    public function getBaseUrl(): string;

    public function withToken(string $token): static;
}
