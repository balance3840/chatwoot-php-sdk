<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Integration;

use PHPUnit\Framework\TestCase;
use RamiroEstrella\ChatwootPhpSdk\ChatwootClient;

/**
 * Base class for integration tests.
 *
 * Reads credentials from environment variables and skips the whole
 * suite if they are not set, so integration tests never accidentally
 * run in CI without credentials.
 *
 * Required environment variables:
 *   CHATWOOT_BASE_URL      e.g. https://chat.example.com
 *   CHATWOOT_API_TOKEN     Agent user api_access_token
 *   CHATWOOT_ACCOUNT_ID    Numeric account ID (default: 1)
 *
 * Optional:
 *   CHATWOOT_INBOX_ID        Numeric inbox ID for conversation tests
 *   CHATWOOT_INBOX_IDENTIFIER  Inbox identifier string for Client API tests
 *   CHATWOOT_PLATFORM_TOKEN  Platform App token for Platform API tests
 */
abstract class IntegrationTestCase extends TestCase
{
    protected ChatwootClient $client;

    protected static function baseUrl(): string
    {
        return (string) getenv('CHATWOOT_BASE_URL');
    }

    protected static function apiToken(): string
    {
        return (string) getenv('CHATWOOT_API_TOKEN');
    }

    protected static function accountId(): int
    {
        return (int) (getenv('CHATWOOT_ACCOUNT_ID') ?: 1);
    }

    protected static function inboxId(): ?int
    {
        $v = getenv('CHATWOOT_INBOX_ID');
        return $v !== false && $v !== '' ? (int) $v : null;
    }

    protected static function inboxIdentifier(): ?string
    {
        $v = getenv('CHATWOOT_INBOX_IDENTIFIER');
        return $v !== false && $v !== '' ? $v : null;
    }

    protected static function platformToken(): ?string
    {
        $v = getenv('CHATWOOT_PLATFORM_TOKEN');
        return $v !== false && $v !== '' ? $v : null;
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (self::baseUrl() === '' || self::apiToken() === '') {
            $this->markTestSkipped(
                'Integration tests require CHATWOOT_BASE_URL and CHATWOOT_API_TOKEN env vars. ' .
                'Copy .env.integration.example to .env.integration and run: ' .
                'export $(cat .env.integration | xargs) && ./vendor/bin/phpunit --testsuite Integration'
            );
        }

        $this->client = new ChatwootClient(
            self::baseUrl(),
            self::apiToken(),
            self::accountId()
        );
    }

    /**
     * Skip this test if no inbox ID is configured.
     */
    protected function requireInboxId(): int
    {
        $id = self::inboxId();

        if ($id === null) {
            $this->markTestSkipped('This test requires CHATWOOT_INBOX_ID to be set.');
        }

        return $id;
    }

    /**
     * Skip this test if no inbox identifier string is configured.
     */
    protected function requireInboxIdentifier(): string
    {
        $id = self::inboxIdentifier();

        if ($id === null) {
            $this->markTestSkipped('This test requires CHATWOOT_INBOX_IDENTIFIER to be set.');
        }

        return $id;
    }

    /**
     * Skip this test if no platform token is configured.
     */
    protected function requirePlatformToken(): string
    {
        $token = self::platformToken();

        if ($token === null) {
            $this->markTestSkipped('This test requires CHATWOOT_PLATFORM_TOKEN to be set.');
        }

        return $token;
    }
}
