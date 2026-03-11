<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Integration;

use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\CannedResponseDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\ContactDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\ContactInboxDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\ConversationDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\InboxDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\MessageDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\TeamDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\ContactCollection;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\ConversationCollection;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\MessageCollection;
use RamiroEstrella\ChatwootPhpSdk\Enums\AgentRole;
use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationPriority;
use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationStatus;
use RamiroEstrella\ChatwootPhpSdk\Enums\MessageType;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\NotFoundException;

/**
 * Integration tests for the Application API.
 *
 * Run with:
 *   set -a && source .env.integration && set +a
 *   ./vendor/bin/phpunit --testsuite Integration --testdox
 */
class ApplicationApiTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Account
    // -------------------------------------------------------------------------

    public function test_account_show_returns_account_dto(): void
    {
        $account = $this->client->application()->account()->show();

        $this->assertSame(self::accountId(), $account->id);
        $this->assertNotEmpty($account->name);
        $this->assertNotEmpty($account->locale);
    }

    // -------------------------------------------------------------------------
    // Profile
    // -------------------------------------------------------------------------

    public function test_profile_get_returns_current_user(): void
    {
        $profile = $this->client->application()->profile()->get();

        $this->assertArrayHasKey('id', $profile);
        $this->assertArrayHasKey('email', $profile);
        $this->assertArrayHasKey('access_token', $profile);
    }

    // -------------------------------------------------------------------------
    // Agents
    // -------------------------------------------------------------------------

    public function test_agents_list_returns_agents(): void
    {
        $agents = $this->client->application()->agents()->list();

        $this->assertNotEmpty($agents, 'Expected at least one agent in the account');
        $this->assertInstanceOf(AgentDTO::class, $agents[0]);
        $this->assertNotNull($agents[0]->id);
        $this->assertNotNull($agents[0]->email);
        $this->assertContains($agents[0]->role, [AgentRole::Agent, AgentRole::Administrator]);
    }

    // -------------------------------------------------------------------------
    // Contacts — full CRUD lifecycle
    // -------------------------------------------------------------------------

    public function test_contacts_full_lifecycle(): void
    {
        $unique = uniqid('sdk_test_');

        $contact = $this->client->application()->contacts()->create([
            'name'  => "SDK Test {$unique}",
            'email' => "{$unique}@sdk-test.invalid",
        ]);

        $this->assertInstanceOf(ContactDTO::class, $contact);
        $this->assertNotNull($contact->id, 'contact->id should not be null after create');
        $this->assertSame("SDK Test {$unique}", $contact->name);
        $this->assertSame("{$unique}@sdk-test.invalid", $contact->email);

        $contactId = $contact->id;

        try {
            // Show
            $fetched = $this->client->application()->contacts()->show($contactId);
            $this->assertSame($contactId, $fetched->id);
            $this->assertSame($contact->email, $fetched->email);

            // Update
            $updated = $this->client->application()->contacts()->update($contactId, [
                'name' => "SDK Updated {$unique}",
            ]);
            $this->assertSame("SDK Updated {$unique}", $updated->name);

            // Search
            $results = $this->client->application()->contacts()->search($unique);
            $this->assertInstanceOf(ContactCollection::class, $results);
            $ids = $results->map(fn (ContactDTO $c) => $c->id);
            $this->assertContains($contactId, $ids);

        } finally {
            $this->client->application()->contacts()->delete($contactId);
        }
    }

    public function test_contacts_list_returns_collection(): void
    {
        $result = $this->client->application()->contacts()->list(['page' => 1]);

        $this->assertInstanceOf(ContactCollection::class, $result);
        $this->assertGreaterThanOrEqual(0, $result->count);
    }

    // -------------------------------------------------------------------------
    // Inboxes
    // -------------------------------------------------------------------------

    public function test_inboxes_list_returns_inboxes(): void
    {
        $inboxes = $this->client->application()->inboxes()->list();

        $this->assertNotEmpty($inboxes, 'Expected at least one inbox in the account');
        $this->assertInstanceOf(InboxDTO::class, $inboxes[0]);
        $this->assertNotNull($inboxes[0]->id);
        $this->assertNotNull($inboxes[0]->name);
    }

    public function test_inbox_show_returns_correct_inbox(): void
    {
        $inboxId = $this->requireInboxId();

        $inbox = $this->client->application()->inboxes()->show($inboxId);

        $this->assertInstanceOf(InboxDTO::class, $inbox);
        $this->assertSame($inboxId, $inbox->id);
        $this->assertNotEmpty($inbox->name);
    }

    // -------------------------------------------------------------------------
    // Conversations — full lifecycle
    // -------------------------------------------------------------------------

    public function test_conversations_full_lifecycle(): void
    {
        $inboxId = $this->requireInboxId();
        $unique  = uniqid('sdk_test_');

        $contact = $this->client->application()->contacts()->create([
            'name'  => "Conv Test {$unique}",
            'email' => "{$unique}@sdk-test.invalid",
        ]);

        $this->assertNotNull($contact->id, 'contact->id must not be null');
        $contactId = $contact->id;

        $contactInbox = $this->client->application()->contacts()->createContactInbox($contactId, $inboxId);
        $this->assertInstanceOf(ContactInboxDTO::class, $contactInbox);
        $this->assertNotEmpty($contactInbox->source_id);

        $conversation = $this->client->application()->conversations()->create([
            'source_id' => $contactInbox->source_id,
            'inbox_id'  => $inboxId,
        ]);

        $this->assertInstanceOf(ConversationDTO::class, $conversation);
        $this->assertNotNull($conversation->id);
        // Chatwoot 4.x may create conversations as 'pending' or 'open' depending on inbox config
        $this->assertContains($conversation->status, [ConversationStatus::Open, ConversationStatus::Pending]);

        $convId = $conversation->id;

        try {
            // Show
            $fetched = $this->client->application()->conversations()->show($convId);
            $this->assertSame($convId, $fetched->id);
            $this->assertNotNull($fetched->meta);

            // Toggle status → resolved
            $resolved = $this->client->application()->conversations()->toggleStatus($convId, ConversationStatus::Resolved);
            $this->assertSame(ConversationStatus::Resolved, $resolved->status);

            // Toggle status back → open
            $reopened = $this->client->application()->conversations()->toggleStatus($convId, ConversationStatus::Open);
            $this->assertSame(ConversationStatus::Open, $reopened->status);

            // Toggle priority
            $prioritized = $this->client->application()->conversations()->togglePriority($convId, ConversationPriority::High);
            $this->assertSame(ConversationPriority::High, $prioritized->priority);

            // Labels
            $labels = $this->client->application()->conversations()->addLabels($convId, ['sdk-test']);
            $this->assertContains('sdk-test', $labels);
            $listed = $this->client->application()->conversations()->listLabels($convId);
            $this->assertContains('sdk-test', $listed);

            // Messages
            $message = $this->client->application()->messages()->sendText($convId, 'Hello from SDK integration test');
            $this->assertInstanceOf(MessageDTO::class, $message);
            $this->assertSame('Hello from SDK integration test', $message->content);
            $this->assertSame(MessageType::Outgoing, $message->message_type);

            $privateNote = $this->client->application()->messages()->sendPrivateNote($convId, 'Private note');
            $this->assertTrue($privateNote->private);

            $messageList = $this->client->application()->messages()->list($convId);
            $this->assertInstanceOf(MessageCollection::class, $messageList);
            $this->assertGreaterThanOrEqual(1, $messageList->count());

            // Conversation list
            $list = $this->client->application()->conversations()->list(['inbox_id' => $inboxId]);
            $this->assertInstanceOf(ConversationCollection::class, $list);

        } finally {
            $this->client->application()->conversations()->toggleStatus($convId, ConversationStatus::Resolved);
            $this->client->application()->contacts()->delete($contactId);
        }
    }

    public function test_conversations_list_returns_collection(): void
    {
        $result = $this->client->application()->conversations()->list();

        $this->assertInstanceOf(ConversationCollection::class, $result);
        $this->assertGreaterThanOrEqual(0, $result->count);
    }

    // -------------------------------------------------------------------------
    // Teams
    // -------------------------------------------------------------------------

    public function test_teams_list_returns_array(): void
    {
        $teams = $this->client->application()->teams()->list();

        $this->assertIsArray($teams);
        if (!empty($teams)) {
            $this->assertInstanceOf(TeamDTO::class, $teams[0]);
            $this->assertNotNull($teams[0]->id);
        }
    }

    public function test_teams_full_lifecycle(): void
    {
        $unique = uniqid('sdk_team_');

        $team = $this->client->application()->teams()->create("SDK Team {$unique}", 'Created by integration test');

        $this->assertInstanceOf(TeamDTO::class, $team);
        $this->assertNotNull($team->id);
        // Chatwoot lowercases team names server-side
        $this->assertEqualsIgnoringCase("SDK Team {$unique}", $team->name);

        $teamId = $team->id;

        try {
            $fetched = $this->client->application()->teams()->show($teamId);
            $this->assertSame($teamId, $fetched->id);

            $updated = $this->client->application()->teams()->update($teamId, ['name' => "SDK Team Updated {$unique}"]);
            $this->assertEqualsIgnoringCase("SDK Team Updated {$unique}", $updated->name);

            $agents = $this->client->application()->teams()->listAgents($teamId);
            $this->assertIsArray($agents);

        } finally {
            $this->client->application()->teams()->delete($teamId);
        }
    }

    // -------------------------------------------------------------------------
    // Canned Responses
    // -------------------------------------------------------------------------

    public function test_canned_responses_full_lifecycle(): void
    {
        $unique = uniqid('sdk_');

        $cr = $this->client->application()->cannedResponses()->create(
            "sdk_{$unique}",
            'Hello from SDK integration test!'
        );

        $this->assertInstanceOf(CannedResponseDTO::class, $cr);
        $this->assertNotNull($cr->id);
        $this->assertSame("sdk_{$unique}", $cr->short_code);

        $crId = $cr->id;

        try {
            $list = $this->client->application()->cannedResponses()->list("sdk_{$unique}");
            $this->assertNotEmpty($list);

            $updated = $this->client->application()->cannedResponses()->update($crId, ['content' => 'Updated content']);
            $this->assertSame('Updated content', $updated->content);

        } finally {
            $this->client->application()->cannedResponses()->delete($crId);
        }
    }

    // -------------------------------------------------------------------------
    // Webhooks
    // -------------------------------------------------------------------------

    public function test_webhooks_full_lifecycle(): void
    {
        // Include timestamp so re-runs after a crash don't hit "URL already taken"
        $unique  = uniqid('', true);
        $url     = "https://webhook.sdk-test.invalid/hook-{$unique}";
        $urlV2   = "https://webhook.sdk-test.invalid/hook-v2-{$unique}";

        $webhook = $this->client->application()->webhooks()->create(
            $url,
            ['conversation_created', 'message_created'],
            'SDK Test Webhook'
        );

        $this->assertNotNull($webhook->id);
        $this->assertSame($url, $webhook->url);
        $this->assertContains('conversation_created', $webhook->subscriptions);

        $webhookId = $webhook->id;

        try {
            $list = $this->client->application()->webhooks()->list();
            $ids  = array_map(fn ($w) => $w->id, $list);
            $this->assertContains($webhookId, $ids);

            $updated = $this->client->application()->webhooks()->update(
                $webhookId,
                $urlV2,
                ['contact_created']
            );
            $this->assertSame($urlV2, $updated->url);

        } finally {
            $this->client->application()->webhooks()->delete($webhookId);
        }
    }

    // -------------------------------------------------------------------------
    // Custom Attributes
    // -------------------------------------------------------------------------

    public function test_custom_attributes_list_returns_array(): void
    {
        $attrs = $this->client->application()->customAttributes()->list(0);

        $this->assertIsArray($attrs);
    }

    // -------------------------------------------------------------------------
    // Audit Logs
    // -------------------------------------------------------------------------

    public function test_audit_logs_list_returns_array(): void
    {
        try {
            $logs = $this->client->application()->auditLogs()->list();
            $this->assertIsArray($logs);
        } catch (NotFoundException $e) {
            $this->markTestSkipped('Audit logs endpoint returned 404 — requires super-admin role.');
        }
    }

    // -------------------------------------------------------------------------
    // Reports
    // -------------------------------------------------------------------------

    public function test_reports_summary_returns_data(): void
    {
        // The summary endpoint requires since/until in some Chatwoot versions
        $since = strtotime('-30 days');
        $until = time();

        try {
            $summary = $this->client->application()->reports()->summary([
                'type'  => 'account',
                'since' => $since,
                'until' => $until,
            ]);

            $this->assertIsArray($summary);

        } catch (NotFoundException $e) {
            $this->markTestSkipped('Reports summary endpoint not available on this Chatwoot instance.');
        }
    }

    public function test_reports_account_conversation_metrics_returns_data(): void
    {
        // v2 reports endpoint requires since/until
        $since = strtotime('-30 days');
        $until = time();

        try {
            $result = $this->client->application()->reports()->accountConversationMetrics([
                'since' => $since,
                'until' => $until,
            ]);

            $this->assertIsArray($result);

        } catch (\RamiroEstrella\ChatwootPhpSdk\Exceptions\ApiException $e) {
            $this->markTestSkipped("Reports v2 not available: {$e->getMessage()}");
        }
    }

    // -------------------------------------------------------------------------
    // Contact Labels
    // -------------------------------------------------------------------------

    public function test_contact_labels_lifecycle(): void
    {
        $unique  = uniqid('sdk_lbl_');
        $contact = $this->client->application()->contacts()->create([
            'name'  => "Label Test {$unique}",
            'email' => "{$unique}@sdk-test.invalid",
        ]);

        $this->assertNotNull($contact->id, 'contact->id must not be null');
        $contactId = $contact->id;

        try {
            $this->client->application()->contactLabels()->update($contactId, ['sdk-integration']);
            $labels = $this->client->application()->contactLabels()->list($contactId);

            $this->assertArrayHasKey('payload', $labels);
            $this->assertContains('sdk-integration', $labels['payload']);

        } finally {
            $this->client->application()->contacts()->delete($contactId);
        }
    }

    // -------------------------------------------------------------------------
    // Conversation Assignments
    // -------------------------------------------------------------------------

    public function test_conversation_assignment_lifecycle(): void
    {
        $inboxId = $this->requireInboxId();
        $unique  = uniqid('sdk_asgn_');

        $contact = $this->client->application()->contacts()->create([
            'name'  => "Assign Test {$unique}",
            'email' => "{$unique}@sdk-test.invalid",
        ]);

        $this->assertNotNull($contact->id, 'contact->id must not be null');

        $contactInbox = $this->client->application()->contacts()->createContactInbox($contact->id, $inboxId);
        $conversation = $this->client->application()->conversations()->create([
            'source_id' => $contactInbox->source_id,
            'inbox_id'  => $inboxId,
        ]);
        $convId = $conversation->id;

        try {
            // Ensure conversation is open — assignments endpoint requires open status in 4.x
            $this->client->application()->conversations()->toggleStatus($convId, ConversationStatus::Open);

            $assignee = $this->client->application()->conversationAssignments()->getAssignee($convId);
            $this->assertIsArray($assignee);

            $agents = $this->client->application()->agents()->list();
            if (!empty($agents)) {
                $assigned = $this->client->application()->conversationAssignments()->assignAgent($convId, $agents[0]->id);
                $this->assertIsArray($assigned);
            }

        } catch (\RamiroEstrella\ChatwootPhpSdk\Exceptions\NotFoundException $e) {
            $this->markTestSkipped('Assignments endpoint not available for this conversation/inbox type.');
        } finally {
            $this->client->application()->conversations()->toggleStatus($convId, ConversationStatus::Resolved);
            $this->client->application()->contacts()->delete($contact->id);
        }
    }
}
