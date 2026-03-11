<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\AccountResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\AccountDTO;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class AccountResourceTest extends ResourceTestCase
{
    private AccountResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new AccountResource($this->http, self::ACCOUNT_ID);
    }

    public function test_show_gets_account_details(): void
    {
        $this->expectGet(self::BASE, ApiResponses::account(), []);

        $account = $this->resource->show();

        $this->assertInstanceOf(AccountDTO::class, $account);
        $this->assertSame(1, $account->id);
        $this->assertSame('ACME Support', $account->name);
    }

    public function test_update_patches_account(): void
    {
        $this->expectPatch(self::BASE, ['name' => 'New Name'], ApiResponses::account(['name' => 'New Name']));

        $account = $this->resource->update(['name' => 'New Name']);

        $this->assertInstanceOf(AccountDTO::class, $account);
        $this->assertSame('New Name', $account->name);
    }
}
