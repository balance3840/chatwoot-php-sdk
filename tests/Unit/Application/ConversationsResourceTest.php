<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ConversationsResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\ConversationDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\ConversationCollection;
use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationPriority;
use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationStatus;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class ConversationsResourceTest extends ResourceTestCase
{
    private ConversationsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new ConversationsResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_returns_conversation_collection(): void
    {
        $this->http->method('get')->willReturn(ApiResponses::conversationList(2));

        $result = $this->resource->list();

        $this->assertInstanceOf(ConversationCollection::class, $result);
        $this->assertCount(2, $result->items);
    }

    public function test_list_passes_filters_as_query(): void
    {
        $this->expectGet(
            self::BASE . '/conversations',
            ApiResponses::conversationList(1),
            ['status' => 'open', 'page' => 1]
        );

        $this->resource->list(['status' => 'open', 'page' => 1]);
    }

    public function test_create_posts_and_returns_dto(): void
    {
        $params = ['source_id' => 'src_abc', 'inbox_id' => 3];
        $this->expectPost(self::BASE . '/conversations', $params, ApiResponses::conversation());

        $conv = $this->resource->create($params);

        $this->assertInstanceOf(ConversationDTO::class, $conv);
        $this->assertSame(100, $conv->id);
        $this->assertSame(ConversationStatus::Open, $conv->status);
        $this->assertSame(ConversationPriority::High, $conv->priority);
    }

    public function test_filter_posts_payload_with_page(): void
    {
        $payload = [['attribute_key' => 'status', 'values' => ['open']]];
        $this->expectPost(self::BASE . '/conversations/filter?page=1', $payload, ApiResponses::conversationList(1));

        $result = $this->resource->filter($payload);

        $this->assertInstanceOf(ConversationCollection::class, $result);
    }

    public function test_show_gets_correct_uri(): void
    {
        $this->expectGet(self::BASE . '/conversations/100', ApiResponses::conversation(), []);

        $conv = $this->resource->show(100);

        $this->assertInstanceOf(ConversationDTO::class, $conv);
        $this->assertSame(100, $conv->id);
    }

    public function test_show_hydrates_meta(): void
    {
        $this->http->method('get')->willReturn(ApiResponses::conversation());

        $conv = $this->resource->show(100);

        $this->assertNotNull($conv->meta);
        $this->assertSame('Channel::Api', $conv->meta->channel);
        $this->assertFalse($conv->meta->hmac_verified);
    }

    public function test_update_patches_correct_uri(): void
    {
        $this->expectPatch(
            self::BASE . '/conversations/100',
            ['assignee_id' => 1],
            ApiResponses::conversation()
        );

        $conv = $this->resource->update(100, ['assignee_id' => 1]);

        $this->assertInstanceOf(ConversationDTO::class, $conv);
    }

    public function test_toggle_status_with_enum(): void
    {
        $convData = ApiResponses::conversation(['status' => 'resolved']);

        $this->http->expects($this->once())->method('post')->willReturn([]);
        $this->http->expects($this->once())
            ->method('get')
            ->with(self::BASE . '/conversations/100')
            ->willReturn($convData);

        $conv = $this->resource->toggleStatus(100, ConversationStatus::Resolved);

        $this->assertSame(ConversationStatus::Resolved, $conv->status);
    }

    public function test_toggle_status_with_string(): void
    {
        $convData = ApiResponses::conversation(['status' => 'pending']);

        $this->http->expects($this->once())->method('post')->willReturn([]);
        $this->http->expects($this->once())
            ->method('get')
            ->with(self::BASE . '/conversations/100')
            ->willReturn($convData);

        $conv = $this->resource->toggleStatus(100, 'pending');

        $this->assertSame(ConversationStatus::Pending, $conv->status);
    }

    public function test_toggle_status_snoozed_includes_timestamp(): void
    {
        $until    = 1704153600;
        $convData = ApiResponses::conversation(['status' => 'snoozed', 'snoozed_until' => $until]);

        $this->http->expects($this->once())->method('post')->willReturn([]);
        $this->http->expects($this->once())
            ->method('get')
            ->with(self::BASE . '/conversations/100')
            ->willReturn($convData);

        $conv = $this->resource->toggleStatus(100, ConversationStatus::Snoozed, $until);

        $this->assertSame(ConversationStatus::Snoozed, $conv->status);
        $this->assertSame($until, $conv->snoozed_until);
    }

    public function test_toggle_priority_with_enum(): void
    {
        $convData = ApiResponses::conversation(['priority' => 'critical']);

        $this->http->expects($this->once())->method('post')->willReturn([]);
        $this->http->expects($this->once())
            ->method('get')
            ->with(self::BASE . '/conversations/100')
            ->willReturn($convData);

        $conv = $this->resource->togglePriority(100, ConversationPriority::Critical);

        $this->assertSame(ConversationPriority::Critical, $conv->priority);
    }

    public function test_toggle_priority_null_unsets_priority(): void
    {
        $convData = ApiResponses::conversation(['priority' => null]);

        $this->http->expects($this->once())->method('post')->willReturn([]);
        $this->http->expects($this->once())
            ->method('get')
            ->with(self::BASE . '/conversations/100')
            ->willReturn($convData);

        $conv = $this->resource->togglePriority(100, null);

        $this->assertNull($conv->priority);
    }

    public function test_toggle_typing_status(): void
    {
        $this->expectPost(
            self::BASE . '/conversations/100/toggle_typing_status',
            ['typing_status' => 'on'],
            []
        );

        $this->resource->toggleTypingStatus(100, 'on');
    }

    public function test_update_custom_attributes(): void
    {
        $attrs = ['order_id' => 'ORD-123'];
        $this->expectPost(
            self::BASE . '/conversations/100/update_custom_attributes',
            ['custom_attributes' => $attrs],
            ApiResponses::conversation(['custom_attributes' => $attrs])
        );

        $conv = $this->resource->updateCustomAttributes(100, $attrs);

        $this->assertSame($attrs, $conv->custom_attributes);
    }

    public function test_list_labels(): void
    {
        $this->expectGet(
            self::BASE . '/conversations/100/labels',
            ['payload' => ['vip', 'billing']],
            []
        );

        $labels = $this->resource->listLabels(100);

        $this->assertSame(['vip', 'billing'], $labels);
    }

    public function test_add_labels(): void
    {
        $this->expectPost(
            self::BASE . '/conversations/100/labels',
            ['labels' => ['vip']],
            ['payload' => ['vip']]
        );

        $labels = $this->resource->addLabels(100, ['vip']);

        $this->assertSame(['vip'], $labels);
    }

    public function test_reporting_events(): void
    {
        $this->expectGet(self::BASE . '/conversations/100/reporting_events', [], []);

        $this->resource->reportingEvents(100);
    }

    public function test_counts(): void
    {
        $this->expectGet(
            self::BASE . '/conversations/counts',
            ['open' => 5, 'pending' => 2],
            []
        );

        $counts = $this->resource->counts();

        $this->assertSame(['open' => 5, 'pending' => 2], $counts);
    }
}
