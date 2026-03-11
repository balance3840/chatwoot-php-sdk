<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\Http\HttpClientInterface;

abstract class BaseResource
{
    protected HttpClientInterface $http;
    protected int $accountId;

    public function __construct(HttpClientInterface $http, int $accountId)
    {
        $this->http      = $http;
        $this->accountId = $accountId;
    }

    /**
     * Build a base path for account-scoped endpoints.
     */
    protected function basePath(string $suffix = ''): string
    {
        $path = "/api/v1/accounts/{$this->accountId}";

        if ($suffix !== '') {
            $path .= '/' . ltrim($suffix, '/');
        }

        return $path;
    }

    /**
     * Build a base path for v2 account-scoped endpoints.
     */
    protected function basePathV2(string $suffix = ''): string
    {
        $path = "/api/v2/accounts/{$this->accountId}";

        if ($suffix !== '') {
            $path .= '/' . ltrim($suffix, '/');
        }

        return $path;
    }

    /**
     * Strip null values from an array before sending as request body.
     */
    protected function filterParams(array $params): array
    {
        return array_filter($params, fn ($value) => $value !== null);
    }

    /**
     * Normalise a single-resource API response.
     *
     * Chatwoot wraps single-resource responses differently across versions:
     *   - v4.x:  { "payload": { "id": 1, ... } }
     *   - v4.x:  { "<resource>": { "id": 1, ... } }  (e.g. "contact", "webhook")
     *   - older: flat object { "id": 1, ... }
     *
     * Pass the optional $resourceKey hint (e.g. "contact", "webhook") when the
     * API is known to use a named wrapper, so it is tried first.
     */
    protected function unwrapSingle(array $data, string $resourceKey = ''): array
    {
        // { "payload": { "contact": {...} } }  — Chatwoot 4.11.x
        if ($resourceKey !== ''
            && isset($data['payload'][$resourceKey])
            && is_array($data['payload'][$resourceKey])
        ) {
            return $data['payload'][$resourceKey];
        }

        // { "payload": { "id": ... } }
        if (isset($data['payload']) && is_array($data['payload']) && isset($data['payload']['id'])) {
            return $data['payload'];
        }

        // { "contact": {...} }  — older versions
        if ($resourceKey !== '' && isset($data[$resourceKey]) && is_array($data[$resourceKey])) {
            return $data[$resourceKey];
        }

        // Already a flat resource object
        return $data;
    }
}
