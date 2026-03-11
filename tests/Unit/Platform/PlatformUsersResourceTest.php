<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Platform;

use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;
use RamiroEstrella\ChatwootPhpSdk\Platform\Resources\PlatformUsersResource;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;

class PlatformUsersResourceTest extends PlatformResourceTestCase
{
    private PlatformUsersResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new PlatformUsersResource($this->http, self::TOKEN);
    }

    public function test_create_posts_to_platform_users(): void
    {
        $params = ['name' => 'Alice', 'email' => 'alice@example.com', 'password' => 'secret'];
        $this->expectPlatform('POST', self::BASE . '/users', ApiResponses::agent());

        $user = $this->resource->create($params);

        $this->assertInstanceOf(AgentDTO::class, $user);
        $this->assertSame('Alice Smith', $user->name);
    }

    public function test_show_gets_user_by_id(): void
    {
        $this->expectPlatform('GET', self::BASE . '/users/1', ApiResponses::agent());

        $user = $this->resource->show(1);

        $this->assertInstanceOf(AgentDTO::class, $user);
        $this->assertSame(1, $user->id);
    }

    public function test_update_patches_user(): void
    {
        $this->expectPlatform('PATCH', self::BASE . '/users/1', ApiResponses::agent(['name' => 'Alice Updated']));

        $user = $this->resource->update(1, ['name' => 'Alice Updated']);

        $this->assertInstanceOf(AgentDTO::class, $user);
        $this->assertSame('Alice Updated', $user->name);
    }

    public function test_update_filters_null_params(): void
    {
        $this->http
            ->expects($this->once())
            ->method('requestWithToken')
            ->with(
                'PATCH',
                self::BASE . '/users/1',
                self::TOKEN,
                $this->callback(function (array $options): bool {
                    // null values must not appear in the body
                    $body = $options[\GuzzleHttp\RequestOptions::JSON] ?? [];
                    return !array_key_exists('display_name', $body)
                        && isset($body['name']);
                })
            )
            ->willReturn(ApiResponses::agent());

        $this->resource->update(1, ['name' => 'Alice', 'display_name' => null]);
    }

    public function test_delete_calls_correct_uri(): void
    {
        $this->expectPlatform('DELETE', self::BASE . '/users/1', []);

        $result = $this->resource->delete(1);

        $this->assertSame([], $result);
    }

    public function test_get_login_url_calls_correct_uri(): void
    {
        $loginData = ['url' => 'https://app.chatwoot.com/auth/sign_in?token=abc'];
        $this->expectPlatform('GET', self::BASE . '/users/1/login', $loginData);

        $result = $this->resource->getLoginUrl(1);

        $this->assertSame($loginData, $result);
    }
}
