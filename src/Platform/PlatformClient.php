<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Platform;

use RamiroEstrella\ChatwootPhpSdk\Http\HttpClient;
use RamiroEstrella\ChatwootPhpSdk\Platform\Resources\PlatformAccountsResource;
use RamiroEstrella\ChatwootPhpSdk\Platform\Resources\PlatformAccountUsersResource;
use RamiroEstrella\ChatwootPhpSdk\Platform\Resources\PlatformAgentBotsResource;
use RamiroEstrella\ChatwootPhpSdk\Platform\Resources\PlatformUsersResource;

/**
 * Platform API Client
 *
 * Entry point for all Platform API resources.
 * Requires a Platform App access_token from Super Admin Console.
 * Available on Self-hosted / Managed Hosting installations ONLY.
 *
 * IMPORTANT: Platform APIs can only access accounts/users created by THIS
 * Platform App's key, or objects explicitly permitted via PlatformAppPermissible.
 *
 * Usage:
 *   $chatwoot->platform('platform_token')->accounts()->create([...])
 *   $chatwoot->platform('platform_token')->users()->create([...])
 *   $chatwoot->platform('platform_token')->accountUsers()->create(1, 2, 'agent')
 *   $chatwoot->platform('platform_token')->agentBots()->list()
 */
class PlatformClient
{
    private HttpClient $http;
    private string $platformToken;

    private ?PlatformAccountsResource $accountsResource         = null;
    private ?PlatformAccountUsersResource $accountUsersResource = null;
    private ?PlatformUsersResource $usersResource               = null;
    private ?PlatformAgentBotsResource $agentBotsResource       = null;

    public function __construct(HttpClient $http, string $platformToken)
    {
        $this->http          = $http;
        $this->platformToken = $platformToken;
    }

    /**
     * Manage accounts (create, read, update, delete).
     */
    public function accounts(): PlatformAccountsResource
    {
        return $this->accountsResource ??= new PlatformAccountsResource($this->http, $this->platformToken);
    }

    /**
     * Manage user membership in accounts (add/remove users and roles).
     */
    public function accountUsers(): PlatformAccountUsersResource
    {
        return $this->accountUsersResource ??= new PlatformAccountUsersResource($this->http, $this->platformToken);
    }

    /**
     * Manage users at the installation level (create, read, update, delete, SSO login).
     */
    public function users(): PlatformUsersResource
    {
        return $this->usersResource ??= new PlatformUsersResource($this->http, $this->platformToken);
    }

    /**
     * Manage platform-level agent bots.
     */
    public function agentBots(): PlatformAgentBotsResource
    {
        return $this->agentBotsResource ??= new PlatformAgentBotsResource($this->http, $this->platformToken);
    }
}
