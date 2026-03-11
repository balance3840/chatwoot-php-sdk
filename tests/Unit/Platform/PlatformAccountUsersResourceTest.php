<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Platform;

use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;
use RamiroEstrella\ChatwootPhpSdk\Enums\AgentRole;
use RamiroEstrella\ChatwootPhpSdk\Platform\Resources\PlatformAccountUsersResource;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;

class PlatformAccountUsersResourceTest extends PlatformResourceTestCase
{
    private PlatformAccountUsersResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new PlatformAccountUsersResource($this->http, self::TOKEN);
    }

    public function test_list_gets_account_users(): void
    {
        $this->expectPlatform('GET', self::BASE . '/accounts/1/account_users', [ApiResponses::agent()]);

        $users = $this->resource->list(1);

        $this->assertCount(1, $users);
        $this->assertInstanceOf(AgentDTO::class, $users[0]);
        $this->assertSame('Alice Smith', $users[0]->name);
        $this->assertSame(AgentRole::Agent, $users[0]->role);
    }

    public function test_list_returns_empty_array_when_response_empty(): void
    {
        $this->http->method('requestWithToken')->willReturn([]);

        $users = $this->resource->list(1);

        $this->assertSame([], $users);
    }

    public function test_create_posts_user_id_and_role(): void
    {
        $this->expectPlatform('POST', self::BASE . '/accounts/1/account_users', ApiResponses::agent());

        $user = $this->resource->create(1, 42, 'agent');

        $this->assertInstanceOf(AgentDTO::class, $user);
    }

    public function test_create_defaults_role_to_agent(): void
    {
        // Capture the options passed to requestWithToken
        $this->http
            ->expects($this->once())
            ->method('requestWithToken')
            ->with(
                'POST',
                self::BASE . '/accounts/1/account_users',
                self::TOKEN,
                $this->callback(function (array $options): bool {
                    return isset($options[\GuzzleHttp\RequestOptions::JSON]['role'])
                        && $options[\GuzzleHttp\RequestOptions::JSON]['role'] === 'agent';
                })
            )
            ->willReturn(ApiResponses::agent());

        $this->resource->create(1, 42);
    }

    public function test_create_with_administrator_role(): void
    {
        $this->http
            ->expects($this->once())
            ->method('requestWithToken')
            ->with(
                'POST',
                self::BASE . '/accounts/1/account_users',
                self::TOKEN,
                $this->callback(function (array $options): bool {
                    return isset($options[\GuzzleHttp\RequestOptions::JSON]['role'])
                        && $options[\GuzzleHttp\RequestOptions::JSON]['role'] === 'administrator';
                })
            )
            ->willReturn(ApiResponses::agent(['role' => 'administrator']));

        $user = $this->resource->create(1, 42, 'administrator');

        $this->assertSame(AgentRole::Administrator, $user->role);
    }

    public function test_delete_sends_user_id_in_body(): void
    {
        $this->http
            ->expects($this->once())
            ->method('requestWithToken')
            ->with(
                'DELETE',
                self::BASE . '/accounts/1/account_users',
                self::TOKEN,
                $this->callback(function (array $options): bool {
                    return isset($options[\GuzzleHttp\RequestOptions::JSON]['user_id'])
                        && $options[\GuzzleHttp\RequestOptions::JSON]['user_id'] === 42;
                })
            )
            ->willReturn([]);

        $this->resource->delete(1, 42);
    }
}
