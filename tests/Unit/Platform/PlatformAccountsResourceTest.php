<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Platform;

use RamiroEstrella\ChatwootPhpSdk\DTO\AccountDTO;
use RamiroEstrella\ChatwootPhpSdk\Platform\Resources\PlatformAccountsResource;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;

class PlatformAccountsResourceTest extends PlatformResourceTestCase
{
    private PlatformAccountsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new PlatformAccountsResource($this->http, self::TOKEN);
    }

    public function test_create_posts_to_platform_accounts(): void
    {
        $params = ['name' => 'ACME Corp'];
        $this->expectPlatform('POST', self::BASE . '/accounts', ApiResponses::account());

        $account = $this->resource->create($params);

        $this->assertInstanceOf(AccountDTO::class, $account);
        $this->assertSame(1, $account->id);
        $this->assertSame('ACME Support', $account->name);
    }

    public function test_show_gets_account_by_id(): void
    {
        $this->expectPlatform('GET', self::BASE . '/accounts/1', ApiResponses::account());

        $account = $this->resource->show(1);

        $this->assertInstanceOf(AccountDTO::class, $account);
        $this->assertSame(1, $account->id);
    }

    public function test_update_patches_account(): void
    {
        $this->expectPlatform('PATCH', self::BASE . '/accounts/1', ApiResponses::account(['name' => 'New Name']));

        $account = $this->resource->update(1, ['name' => 'New Name']);

        $this->assertInstanceOf(AccountDTO::class, $account);
        $this->assertSame('New Name', $account->name);
    }

    public function test_update_filters_null_params(): void
    {
        // null values should be stripped before sending
        $this->expectPlatform('PATCH', self::BASE . '/accounts/1', ApiResponses::account());

        // 'locale' => null should be filtered out silently
        $this->resource->update(1, ['name' => 'ACME', 'locale' => null]);
    }

    public function test_delete_calls_correct_uri(): void
    {
        $this->expectPlatform('DELETE', self::BASE . '/accounts/1', []);

        $result = $this->resource->delete(1);

        $this->assertSame([], $result);
    }
}
