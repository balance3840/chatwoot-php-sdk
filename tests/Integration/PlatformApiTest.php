<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Integration;

use RamiroEstrella\ChatwootPhpSdk\DTO\AccountDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;
use SebastianBergmann\CodeCoverage\Report\PHP;

/**
 * Integration tests for the Platform API.
 *
 * Self-hosted only. Requires a Platform App token from the Super Admin Console.
 *
 * Required env var:
 *   CHATWOOT_PLATFORM_TOKEN   From Super Admin Console → Platform Apps → [your app] → Access Token
 */
class PlatformApiTest extends IntegrationTestCase
{
    public function test_platform_users_full_lifecycle(): void
    {
        $platformToken = $this->requirePlatformToken();
        $unique = uniqid('sdk_platform_');
        $platform = $this->client->platform($platformToken);

        $user = $platform->users()->create([
            'name' => "SDK Platform User {$unique}",
            'email' => "{$unique}@sdk-test.invalid",
            'password' => 'SDK_Test_Password_123!',
            'password_confirmation' => 'SDK_Test_Password_123!',
        ]);

        $this->assertInstanceOf(AgentDTO::class, $user);
        $this->assertNotNull($user->id);
        $this->assertSame("SDK Platform User {$unique}", $user->name);
        $this->assertSame("{$unique}@sdk-test.invalid", $user->email);

        $userId = $user->id;

        try {
            $fetched = $platform->users()->show($userId);
            $this->assertSame($userId, $fetched->id);
            $this->assertSame($user->email, $fetched->email);

            $updated = $platform->users()->update($userId, ['name' => "SDK Platform Updated {$unique}"]);
            $this->assertSame("SDK Platform Updated {$unique}", $updated->name);

            // SSO login URL — Chatwoot returns a URL with sso_auth_token param
            $loginData = $platform->users()->getLoginUrl($userId);
            $this->assertIsArray($loginData);
            $this->assertArrayHasKey('url', $loginData);
            $this->assertStringContainsString('sso_auth_token', $loginData['url']);

        } finally {
            $platform->users()->delete($userId);
        }
    }

    public function test_platform_accounts_full_lifecycle(): void
    {
        $platformToken = $this->requirePlatformToken();
        $unique = uniqid('sdk_acct_');
        $platform = $this->client->platform($platformToken);

        $account = $platform->accounts()->create(['name' => "SDK Test Account {$unique}"]);

        $this->assertInstanceOf(AccountDTO::class, $account);
        $this->assertNotNull($account->id);
        $this->assertSame("SDK Test Account {$unique}", $account->name);

        $accountId = $account->id;

        try {
            $fetched = $platform->accounts()->show($accountId);
            $this->assertSame($accountId, $fetched->id);

            $updated = $platform->accounts()->update($accountId, ['name' => "SDK Test Account Updated {$unique}"]);
            $this->assertSame("SDK Test Account Updated {$unique}", $updated->name);

        } finally {
            $platform->accounts()->delete($accountId);
        }
    }

    public function test_platform_account_users_lifecycle(): void
    {
        $platformToken = $this->requirePlatformToken();
        $unique = uniqid('sdk_acct_usr_');
        $platform = $this->client->platform($platformToken);

        $account = $platform->accounts()->create(['name' => "SDK AccountUsers Test {$unique}"]);
        $user = $platform->users()->create([
            'name' => "SDK AccountUser {$unique}",
            'email' => "{$unique}@sdk-test.invalid",
            'password' => 'SDK_Test_Password_123!',
            'password_confirmation' => 'SDK_Test_Password_123!',
        ]);

        $accountId = $account->id;
        $userId = $user->id;

        try {
            // Add user to account
            $accountUser = $platform->accountUsers()->create($accountId, $userId, 'agent');
            $this->assertInstanceOf(AgentDTO::class, $accountUser);

            // List users — verify the user was added (at least one user should now be in the account)
            $users = $platform->accountUsers()->list($accountId);
            $this->assertIsArray($users);
            $this->assertNotEmpty($users, 'Account should have at least the user we just added');

            // Remove user from account
            $platform->accountUsers()->delete($accountId, $userId);

        } finally {
            $platform->users()->delete($userId);
            $platform->accounts()->delete($accountId);
        }
    }

    public function test_platform_agent_bots_lifecycle(): void
    {
        $platformToken = $this->requirePlatformToken();
        $unique = uniqid('sdk_bot_');
        $platform = $this->client->platform($platformToken);

        try {
            $bot = $platform->agentBots()->create(['name' => "SDK Bot {$unique}"]);
        } catch (\RamiroEstrella\ChatwootPhpSdk\Exceptions\ApiException $e) {
            $this->markTestSkipped("Platform agent bots not available on this instance: {$e->getMessage()}");
        }

        $this->assertNotNull($bot->id);
        $this->assertSame("SDK Bot {$unique}", $bot->name);

        $botId = $bot->id;

        try {
            $fetched = $platform->agentBots()->show($botId);
            $this->assertSame($botId, $fetched->id);

            $updated = $platform->agentBots()->update($botId, ['name' => "SDK Bot Updated {$unique}"]);
            $this->assertSame("SDK Bot Updated {$unique}", $updated->name);
            try {
                $list = $platform->agentBots()->list();
                $ids = array_map(fn($b) => $b->id, $list);
                $this->assertContains($botId, $ids);
            } catch (\RamiroEstrella\ChatwootPhpSdk\Exceptions\ApiException $e) {
                $this->markTestSkipped("Platform agents list has a bug tested in Chatwoot version 4.11.2: {$e->getMessage()}");
            }

        } finally {
            if (isset($botId)) {
                $platform->agentBots()->delete($botId);
            }
        }
    }
}
