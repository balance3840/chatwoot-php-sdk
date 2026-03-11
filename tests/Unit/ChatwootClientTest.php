<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RamiroEstrella\ChatwootPhpSdk\Application\ApplicationClient;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\AgentsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ContactsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ConversationsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\InboxesResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\MessagesResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\TeamsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\WebhooksResource;
use RamiroEstrella\ChatwootPhpSdk\Client\ClientApiClient;
use RamiroEstrella\ChatwootPhpSdk\Client\Resources\ContactsApiResource;
use RamiroEstrella\ChatwootPhpSdk\Client\Resources\ConversationsApiResource;
use RamiroEstrella\ChatwootPhpSdk\Client\Resources\MessagesApiResource;
use RamiroEstrella\ChatwootPhpSdk\ChatwootClient;
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClient;
use RamiroEstrella\ChatwootPhpSdk\Platform\PlatformClient;
use RamiroEstrella\ChatwootPhpSdk\Platform\Resources\PlatformAccountsResource;
use RamiroEstrella\ChatwootPhpSdk\Platform\Resources\PlatformUsersResource;

class ChatwootClientTest extends TestCase
{
    private ChatwootClient $client;

    protected function setUp(): void
    {
        $this->client = new ChatwootClient('https://chat.example.com', 'token_abc', 1);
    }

    // ------------------------------------------------------------------
    // Construction
    // ------------------------------------------------------------------

    public function test_exposes_http_client(): void
    {
        $this->assertInstanceOf(HttpClient::class, $this->client->getHttpClient());
    }

    // ------------------------------------------------------------------
    // application() — wiring
    // ------------------------------------------------------------------

    public function test_application_returns_application_client(): void
    {
        $this->assertInstanceOf(ApplicationClient::class, $this->client->application());
    }

    public function test_application_is_lazily_cached(): void
    {
        $a = $this->client->application();
        $b = $this->client->application();

        $this->assertSame($a, $b);
    }

    public function test_application_contacts_returns_contacts_resource(): void
    {
        $this->assertInstanceOf(ContactsResource::class, $this->client->application()->contacts());
    }

    public function test_application_conversations_returns_conversations_resource(): void
    {
        $this->assertInstanceOf(ConversationsResource::class, $this->client->application()->conversations());
    }

    public function test_application_messages_returns_messages_resource(): void
    {
        $this->assertInstanceOf(MessagesResource::class, $this->client->application()->messages());
    }

    public function test_application_agents_returns_agents_resource(): void
    {
        $this->assertInstanceOf(AgentsResource::class, $this->client->application()->agents());
    }

    public function test_application_inboxes_returns_inboxes_resource(): void
    {
        $this->assertInstanceOf(InboxesResource::class, $this->client->application()->inboxes());
    }

    public function test_application_teams_returns_teams_resource(): void
    {
        $this->assertInstanceOf(TeamsResource::class, $this->client->application()->teams());
    }

    public function test_application_webhooks_returns_webhooks_resource(): void
    {
        $this->assertInstanceOf(WebhooksResource::class, $this->client->application()->webhooks());
    }

    public function test_application_resources_are_lazily_cached(): void
    {
        $a = $this->client->application()->contacts();
        $b = $this->client->application()->contacts();

        $this->assertSame($a, $b);
    }

    // ------------------------------------------------------------------
    // client() — wiring
    // ------------------------------------------------------------------

    public function test_client_returns_client_api_client(): void
    {
        $this->assertInstanceOf(ClientApiClient::class, $this->client->client('inbox_xyz'));
    }

    public function test_client_returns_new_instance_per_call(): void
    {
        // Each call may use a different inbox identifier
        $a = $this->client->client('inbox_a');
        $b = $this->client->client('inbox_b');

        $this->assertNotSame($a, $b);
    }

    public function test_client_contacts_returns_contacts_api_resource(): void
    {
        $this->assertInstanceOf(ContactsApiResource::class, $this->client->client('inbox_xyz')->contacts());
    }

    public function test_client_conversations_returns_conversations_api_resource(): void
    {
        $resource = $this->client->client('inbox_xyz')->conversations('src_contact');

        $this->assertInstanceOf(ConversationsApiResource::class, $resource);
    }

    public function test_client_messages_returns_messages_api_resource(): void
    {
        $resource = $this->client->client('inbox_xyz')->messages('src_contact');

        $this->assertInstanceOf(MessagesApiResource::class, $resource);
    }

    // ------------------------------------------------------------------
    // platform() — wiring
    // ------------------------------------------------------------------

    public function test_platform_returns_platform_client(): void
    {
        $this->assertInstanceOf(PlatformClient::class, $this->client->platform('plat_token'));
    }

    public function test_platform_cached_when_called_without_token(): void
    {
        $a = $this->client->platform('plat_token');
        $b = $this->client->platform();

        $this->assertSame($a, $b);
    }

    public function test_platform_refreshed_when_new_token_provided(): void
    {
        $a = $this->client->platform('token_one');
        $b = $this->client->platform('token_two');

        $this->assertNotSame($a, $b);
    }

    public function test_platform_accounts_returns_correct_resource(): void
    {
        $this->assertInstanceOf(
            PlatformAccountsResource::class,
            $this->client->platform('tok')->accounts()
        );
    }

    public function test_platform_users_returns_correct_resource(): void
    {
        $this->assertInstanceOf(
            PlatformUsersResource::class,
            $this->client->platform('tok')->users()
        );
    }
}
