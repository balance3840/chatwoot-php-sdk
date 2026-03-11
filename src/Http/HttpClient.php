<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\ApiException;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\AuthenticationException;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\NotFoundException;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\ValidationException;

class HttpClient implements HttpClientInterface
{
    private Client $guzzle;
    private string $apiToken;
    private string $baseUrl;

    public function __construct(string $baseUrl, string $apiToken, array $options = [])
    {
        $this->baseUrl  = rtrim($baseUrl, '/');
        $this->apiToken = $apiToken;

        $defaultOptions = [
            'base_uri' => $this->baseUrl,
            'headers'  => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'timeout' => 30,
        ];

        $this->guzzle = new Client(array_merge($defaultOptions, $options));
    }

    public function get(string $uri, array $query = [], array $headers = []): array
    {
        return $this->request('GET', $uri, [
            RequestOptions::QUERY   => $query,
            RequestOptions::HEADERS => $headers,
        ]);
    }

    public function post(string $uri, array $body = [], array $headers = []): array
    {
        return $this->request('POST', $uri, [
            RequestOptions::JSON    => $body,
            RequestOptions::HEADERS => $headers,
        ]);
    }

    public function postMultipart(string $uri, array $multipart, array $headers = []): array
    {
        return $this->request('POST', $uri, [
            RequestOptions::MULTIPART => $multipart,
            RequestOptions::HEADERS   => $headers,
        ]);
    }

    public function put(string $uri, array $body = [], array $headers = []): array
    {
        return $this->request('PUT', $uri, [
            RequestOptions::JSON    => $body,
            RequestOptions::HEADERS => $headers,
        ]);
    }

    public function patch(string $uri, array $body = [], array $headers = []): array
    {
        return $this->request('PATCH', $uri, [
            RequestOptions::JSON    => $body,
            RequestOptions::HEADERS => $headers,
        ]);
    }

    public function delete(string $uri, array $body = [], array $headers = []): array
    {
        return $this->request('DELETE', $uri, [
            RequestOptions::JSON    => $body,
            RequestOptions::HEADERS => $headers,
        ]);
    }

    private function request(string $method, string $uri, array $options = []): array
    {
        // Application API uses api_access_token header
        $options[RequestOptions::HEADERS] = array_merge(
            ['api_access_token' => $this->apiToken],
            $options[RequestOptions::HEADERS] ?? []
        );

        try {
            $response = $this->guzzle->request($method, $uri, $options);
            $body     = (string) $response->getBody();

            if (empty($body)) {
                return [];
            }

            $decoded = json_decode($body, true);

            return is_array($decoded) ? $decoded : [];
        } catch (GuzzleException $e) {
            $this->handleException($e);
        }
    }

    /**
     * Execute a request with an explicit token (Platform API).
     *
     * Platform API uses api_access_token header (same as Application API)
     * but routes are under /platform/api/v1/ instead of /api/v1/
     * Uses a dedicated client to avoid token contamination.
     */
    public function requestWithToken(
        string $method,
        string $uri,
        string $token,
        array $options = []
    ): array {
        $client = new Client([
            'base_uri' => $this->baseUrl,
            'headers'  => [
                'Accept'           => 'application/json',
                'Content-Type'     => 'application/json',
                'api_access_token' => $token,
            ],
            'timeout' => 30,
        ]);

        try {
            $response = $client->request($method, $uri, $options);
            $body     = (string) $response->getBody();

            if (empty($body)) {
                return [];
            }

            $decoded = json_decode($body, true);

            return is_array($decoded) ? $decoded : [];
        } catch (GuzzleException $e) {
            $this->handleException($e);
        }
    }

    private function handleException(GuzzleException $e): never
    {
        if (method_exists($e, 'getResponse') && $e->getResponse() !== null) {
            $response   = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $body       = json_decode((string) $response->getBody(), true) ?? [];
            $message    = $body['error'] ?? $body['message'] ?? $e->getMessage();

            match (true) {
                $statusCode === 401 => throw new AuthenticationException($message, $statusCode),
                $statusCode === 404 => throw new NotFoundException($message, $statusCode),
                $statusCode === 422 => throw new ValidationException($message, $statusCode, $body),
                default             => throw new ApiException($message, $statusCode),
            };
        }

        throw new ApiException($e->getMessage(), 0, $e);
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function withToken(string $token): static
    {
        $clone           = clone $this;
        $clone->apiToken = $token;

        return $clone;
    }
}
