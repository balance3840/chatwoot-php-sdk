<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Integration;

use RamiroEstrella\ChatwootPhpSdk\Exceptions\NotFoundException;

/**
 * Integration tests for the Client (Public) API.
 *
 * The Client API is for end-user chat widgets.
 * The inbox MUST be a "Website" (web widget) type — other inbox types
 * (email, API, phone, etc.) return 404 on these endpoints.
 *
 * Required env var:
 *   CHATWOOT_INBOX_IDENTIFIER   From Settings → Inboxes → [your WebWidget inbox] → Collaboration tab
 *   CHATWOOT_INBOX_ID           Numeric inbox ID (used to clean up contacts via Application API)
 */
class ClientApiTest extends IntegrationTestCase
{
    /**
     * Wraps every Client API call and marks the test as skipped if the inbox
     * identifier is not configured or if the inbox type doesn't support the
     * Client API (returns 404).
     */
    private function runClientTest(callable $test): void
    {
        $this->requireInboxIdentifier();

        try {
            $test();
        } catch (NotFoundException $e) {
            $this->markTestSkipped(
                'Client API returned 404. Ensure CHATWOOT_INBOX_IDENTIFIER points to a ' .
                '"Website" (web widget) inbox, not an email/API/phone inbox.'
            );
        }
    }

    public function test_client_contact_full_lifecycle(): void
    {
        $this->runClientTest(function (): void {
            $inboxIdentifier = self::inboxIdentifier();
            $unique          = uniqid('sdk_client_');
            $clientApi       = $this->client->client($inboxIdentifier);

            $created = $clientApi->contacts()->create([
                'name'  => "Client Test {$unique}",
                'email' => "{$unique}@sdk-test.invalid",
            ]);

            $this->assertArrayHasKey('id', $created);
            $this->assertArrayHasKey('source_id', $created);
            $this->assertArrayHasKey('pubsub_token', $created);
            $this->assertNotEmpty($created['source_id']);

            $sourceId  = $created['source_id'];
            $contactId = $created['id'];

            try {
                // Get by source_id
                $fetched = $clientApi->contacts()->get($sourceId);
                $this->assertSame($sourceId, $fetched['source_id']);
                $this->assertSame("Client Test {$unique}", $fetched['name']);

                // Update
                $updated = $clientApi->contacts()->update($sourceId, [
                    'name' => "Client Updated {$unique}",
                ]);
                $this->assertSame("Client Updated {$unique}", $updated['name']);

            } finally {
                // Client API has no delete — clean up via Application API
                $this->client->application()->contacts()->delete($contactId);
            }
        });
    }

    public function test_client_conversation_full_lifecycle(): void
    {
        $this->runClientTest(function (): void {
            $inboxIdentifier = self::inboxIdentifier();
            $unique          = uniqid('sdk_client_conv_');
            $clientApi       = $this->client->client($inboxIdentifier);

            $contact   = $clientApi->contacts()->create([
                'name'  => "Conv Client Test {$unique}",
                'email' => "{$unique}@sdk-test.invalid",
            ]);
            $sourceId  = $contact['source_id'];
            $contactId = $contact['id'];

            try {
                $convApi = $clientApi->conversations($sourceId);

                $conversation = $convApi->create();
                $this->assertArrayHasKey('id', $conversation);
                $this->assertArrayHasKey('inbox_id', $conversation);
                $convId = $conversation['id'];

                $fetched = $convApi->get($convId);
                $this->assertSame($convId, $fetched['id']);

                $list = $convApi->list();
                $this->assertIsArray($list);
                $ids = array_column($list, 'id');
                $this->assertContains($convId, $ids);

                $convApi->toggleTyping($convId, 'on');
                $convApi->toggleTyping($convId, 'off');
                $convApi->updateLastSeen($convId);

                $resolved = $convApi->resolve($convId);
                $this->assertIsArray($resolved);

            } finally {
                $this->client->application()->contacts()->delete($contactId);
            }
        });
    }

    public function test_client_messages_lifecycle(): void
    {
        $this->runClientTest(function (): void {
            $inboxIdentifier = self::inboxIdentifier();
            $unique          = uniqid('sdk_client_msg_');
            $clientApi       = $this->client->client($inboxIdentifier);

            $contact   = $clientApi->contacts()->create([
                'name'  => "Msg Client Test {$unique}",
                'email' => "{$unique}@sdk-test.invalid",
            ]);
            $sourceId  = $contact['source_id'];
            $contactId = $contact['id'];

            try {
                $conversation = $clientApi->conversations($sourceId)->create();
                $convId       = $conversation['id'];
                $msgApi       = $clientApi->messages($sourceId);

                $sent = $msgApi->send($convId, 'Hello from the SDK client integration test!');
                $this->assertArrayHasKey('id', $sent);
                $this->assertSame('Hello from the SDK client integration test!', $sent['content']);

                $msg2 = $msgApi->create($convId, ['content' => 'Second message', 'message_type' => 'outgoing']);
                $this->assertSame('Second message', $msg2['content']);

                $messages = $msgApi->list($convId);
                $this->assertIsArray($messages);
                $this->assertNotEmpty($messages);

            } finally {
                $this->client->application()->contacts()->delete($contactId);
            }
        });
    }
}
