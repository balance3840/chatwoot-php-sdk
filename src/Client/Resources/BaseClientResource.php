<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Client\Resources;

use RamiroEstrella\ChatwootPhpSdk\Http\HttpClientInterface;

abstract class BaseClientResource
{
    protected HttpClientInterface $http;
    protected string $inboxIdentifier;

    public function __construct(HttpClientInterface $http, string $inboxIdentifier)
    {
        $this->http            = $http;
        $this->inboxIdentifier = $inboxIdentifier;
    }

    /**
     * Base path for public client API endpoints.
     */
    protected function basePath(string $suffix = ''): string
    {
        $path = "/public/api/v1/inboxes/{$this->inboxIdentifier}";

        if ($suffix !== '') {
            $path .= '/' . ltrim($suffix, '/');
        }

        return $path;
    }

    protected function filterParams(array $params): array
    {
        return array_filter($params, fn ($value) => $value !== null);
    }
}
