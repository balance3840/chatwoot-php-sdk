<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ReportsResource;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class ReportsResourceTest extends ResourceTestCase
{
    private const V1_BASE = '/api/v1/accounts/1';
    private const V2_BASE = '/api/v2/accounts/1';

    private ReportsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new ReportsResource($this->http, self::ACCOUNT_ID);
    }

    // ------------------------------------------------------------------
    // V1 endpoints
    // ------------------------------------------------------------------

    public function test_events_calls_correct_endpoint(): void
    {
        $this->expectGet(self::V1_BASE . '/reports/events', ['data' => []], []);

        $result = $this->resource->events();

        $this->assertSame(['data' => []], $result);
    }

    public function test_events_passes_filter_params(): void
    {
        $this->expectGet(self::V1_BASE . '/reports/events', [], ['since' => 1700000000, 'until' => 1700100000]);

        $this->resource->events(['since' => 1700000000, 'until' => 1700100000]);
    }

    public function test_get_calls_correct_endpoint(): void
    {
        $params = ['metric' => 'account', 'type' => 'account', 'since' => 1700000000, 'until' => 1700100000];
        $this->expectGet(self::V1_BASE . '/reports', [['timestamp' => 1700000000, 'value' => '5']], $params);

        $result = $this->resource->get($params);

        $this->assertIsArray($result);
    }

    public function test_get_filters_null_params(): void
    {
        $this->http
            ->expects($this->once())
            ->method('get')
            ->with(
                self::V1_BASE . '/reports',
                $this->callback(fn (array $q) => !array_key_exists('id', $q) && isset($q['metric']))
            )
            ->willReturn([]);

        $this->resource->get(['metric' => 'account', 'id' => null]);
    }

    public function test_summary_calls_correct_endpoint(): void
    {
        $summary = ['avg_first_response_time' => '00:05:00', 'account_id' => 1];
        $this->expectGet(self::V1_BASE . '/reports/summary', $summary, ['type' => 'account']);

        $result = $this->resource->summary(['type' => 'account']);

        $this->assertSame('00:05:00', $result['avg_first_response_time']);
    }

    // ------------------------------------------------------------------
    // V2 endpoints
    // ------------------------------------------------------------------

    public function test_account_conversation_metrics_uses_v2_path(): void
    {
        $this->expectGet(self::V2_BASE . '/reports/conversations', ['open' => 10], []);

        $result = $this->resource->accountConversationMetrics();

        $this->assertSame(['open' => 10], $result);
    }

    public function test_agent_conversation_metrics_uses_v2_path(): void
    {
        $this->expectGet(self::V2_BASE . '/reports/conversations/', [], []);

        $this->resource->agentConversationMetrics();
    }

    public function test_conversations_by_channel_calls_correct_v2_endpoint(): void
    {
        $this->expectGet(self::V2_BASE . '/reports/conversations/channel', ['Channel::Api' => ['open' => 3]], []);

        $result = $this->resource->conversationsByChannel();

        $this->assertArrayHasKey('Channel::Api', $result);
    }

    public function test_conversations_by_channel_passes_date_range(): void
    {
        $this->expectGet(
            self::V2_BASE . '/reports/conversations/channel',
            [],
            ['since' => 1700000000, 'until' => 1700100000]
        );

        $this->resource->conversationsByChannel(['since' => 1700000000, 'until' => 1700100000]);
    }

    public function test_conversations_by_inbox_calls_correct_v2_endpoint(): void
    {
        $this->expectGet(self::V2_BASE . '/reports/conversations/inbox', [], []);

        $this->resource->conversationsByInbox();
    }

    public function test_conversations_by_agent_calls_correct_v2_endpoint(): void
    {
        $this->expectGet(self::V2_BASE . '/reports/conversations/agent', [], []);

        $this->resource->conversationsByAgent();
    }

    public function test_conversations_by_team_calls_correct_v2_endpoint(): void
    {
        $this->expectGet(self::V2_BASE . '/reports/conversations/team', [], []);

        $this->resource->conversationsByTeam();
    }

    public function test_first_response_time_distribution_calls_correct_endpoint(): void
    {
        $this->expectGet(self::V2_BASE . '/reports/first_response', ['data' => []], []);

        $this->resource->firstResponseTimeDistribution();
    }

    public function test_inbox_label_matrix_calls_correct_endpoint(): void
    {
        $this->expectGet(self::V2_BASE . '/reports/inbox_label', [], []);

        $this->resource->inboxLabelMatrix();
    }

    public function test_outgoing_message_counts_calls_correct_endpoint(): void
    {
        $this->expectGet(self::V2_BASE . '/reports/outgoing_messages', [], []);

        $this->resource->outgoingMessageCounts();
    }

    public function test_outgoing_message_counts_passes_params(): void
    {
        $this->expectGet(
            self::V2_BASE . '/reports/outgoing_messages',
            [],
            ['since' => 1700000000, 'group_by' => 'day']
        );

        $this->resource->outgoingMessageCounts(['since' => 1700000000, 'group_by' => 'day']);
    }
}
