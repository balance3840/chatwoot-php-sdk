<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk;

use RamiroEstrella\ChatwootPhpSdk\Application\ApplicationClient;
use RamiroEstrella\ChatwootPhpSdk\Client\ClientApiClient;
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClient;
use RamiroEstrella\ChatwootPhpSdk\Platform\PlatformClient;

/**
 * Chatwoot PHP SDK - Main Client
 *
 * Usage:
 *   $chatwoot = new ChatwootClient('https://app.chatwoot.com', 'your_api_token');
 *
 *   // Application API (agent/admin operations)
 *   $chatwoot->application()->contacts()->list();
 *   $chatwoot->application()->conversations()->create([...]);
 *
 *   // Client API (end-user chat interfaces)
 *   $chatwoot->client('inbox_identifier')->contacts()->create([...]);
 *   $chatwoot->client('inbox_identifier')->conversations('contact_id')->create();
 *
 *   // Platform API (installation-level management, self-hosted only)
 *   $chatwoot->platform()->accounts()->create([...]);
 */
class ChatwootClient
{
    private HttpClient $httpClient;
    private int $accountId;
    private ?ApplicationClient $applicationClient = null;
    private ?PlatformClient $platformClient = null;

    /**
     * @param string $baseUrl    Base URL of your Chatwoot instance (e.g. https://app.chatwoot.com)
     * @param string $apiToken   User access token from Profile Settings
     * @param int    $accountId  Your Chatwoot account numeric ID
     * @param array  $options    Additional Guzzle HTTP client options
     */
    public function __construct(
        string $baseUrl,
        string $apiToken,
        int $accountId = 1,
        array $options = []
    ) {
        $this->accountId = $accountId;
        $this->httpClient = new HttpClient($baseUrl, $apiToken, $options);
    }

    /**
     * Access the Application API.
     * Requires a user api_access_token.
     * Available on Cloud and Self-hosted installations.
     */
    public function application(): ApplicationClient
    {
        if ($this->applicationClient === null) {
            $this->applicationClient = new ApplicationClient(
                $this->httpClient,
                $this->accountId
            );
        }

        return $this->applicationClient;
    }

    /**
     * Access the Client API.
     * Uses inbox_identifier for authentication.
     * Used to build custom chat UIs for end-users.
     *
     * @param string $inboxIdentifier The inbox identifier from Settings → Configuration
     */
    public function client(string $inboxIdentifier): ClientApiClient
    {
        return new ClientApiClient($this->httpClient, $inboxIdentifier);
    }

    /**
     * Access the Platform API.
     * Requires a Platform App access token.
     * Available on Self-hosted installations only.
     *
     * @param string $platformToken Platform App access token from Super Admin Console
     */
    public function platform(string $platformToken = ''): PlatformClient
    {
        if ($this->platformClient === null || $platformToken !== '') {
            $this->platformClient = new PlatformClient(
                $this->httpClient,
                $platformToken
            );
        }

        return $this->platformClient;
    }

    /**
     * Get the underlying HTTP client for advanced usage.
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }
}
