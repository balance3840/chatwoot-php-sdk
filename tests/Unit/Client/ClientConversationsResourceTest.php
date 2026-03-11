<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Client;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RamiroEstrella\ChatwootPhpSdk\Client\Resources\ConversationsApiResource;
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClient;

class ClientConversationsResourceTest extends TestCase
{
    private const INBOX_ID   = 'inbox_abc123';
    private const CONTACT_ID = 'src_contact_xyz';
    private const BASE       = '/public/api/v1/inboxes/inbox_abc123/contacts/src_contact_xyz/conversations';

    private HttpClient&MockObject $http;
    private ConversationsApiResource $resource;

    protected function setUp(): void
    {
        $this->http     = $this->createMock(HttpClient::class);
        $this->resource = new ConversationsApiResource($this->http, self::INBOX_ID, self::CONTACT_ID);
    }

    public function test_list_gets_all_conversations(): void
    {
        $response = [['id' => 1, 'inbox_id' => 3], ['id' => 2, 'inbox_id' => 3]];

        $this->http
            ->expects($this->once())
            ->method('get')
            ->with(self::BASE, [])
            ->willReturn($response);

        $result = $this->resource->list();

        $this->assertCount(2, $result);
    }

    public function test_create_posts_to_conversations(): void
    {
        $response = ['id' => 10, 'inbox_id' => 3, 'messages' => []];

        $this->http
            ->expects($this->once())
            ->method('post')
            ->with(self::BASE, [])
            ->willReturn($response);

        $result = $this->resource->create();

        $this->assertSame(10, $result['id']);
    }

    public function test_create_with_custom_attributes(): void
    {
        $attrs    = ['custom_attributes' => ['order_id' => 'ORD-99']];
        $response = ['id' => 11, 'custom_attributes' => $attrs['custom_attributes']];

        $this->http
            ->expects($this->once())
            ->method('post')
            ->with(self::BASE, $attrs)
            ->willReturn($response);

        $result = $this->resource->create($attrs);

        $this->assertSame(11, $result['id']);
    }

    public function test_get_fetches_single_conversation(): void
    {
        $response = ['id' => 10, 'inbox_id' => 3];

        $this->http
            ->expects($this->once())
            ->method('get')
            ->with(self::BASE . '/10', [])
            ->willReturn($response);

        $result = $this->resource->get(10);

        $this->assertSame(10, $result['id']);
    }

    public function test_resolve_posts_to_resolve_endpoint(): void
    {
        $this->http
            ->expects($this->once())
            ->method('post')
            ->with(self::BASE . '/10/resolve', [])
            ->willReturn(['id' => 10, 'status' => 'resolved']);

        $result = $this->resource->resolve(10);

        $this->assertSame('resolved', $result['status']);
    }

    public function test_toggle_typing_on(): void
    {
        $this->http
            ->expects($this->once())
            ->method('post')
            ->with(self::BASE . '/10/toggle_typing', ['typing_status' => 'on'])
            ->willReturn([]);

        $this->resource->toggleTyping(10, 'on');
    }

    public function test_toggle_typing_off(): void
    {
        $this->http
            ->expects($this->once())
            ->method('post')
            ->with(self::BASE . '/10/toggle_typing', ['typing_status' => 'off'])
            ->willReturn([]);

        $this->resource->toggleTyping(10, 'off');
    }

    public function test_update_last_seen_posts_to_correct_endpoint(): void
    {
        $this->http
            ->expects($this->once())
            ->method('post')
            ->with(self::BASE . '/10/update_last_seen', [])
            ->willReturn([]);

        $this->resource->updateLastSeen(10);
    }

    public function test_paths_scope_to_correct_contact_identifier(): void
    {
        // Ensure a different contact identifier produces a different path
        $altHttp     = $this->createMock(HttpClient::class);
        $altResource = new ConversationsApiResource($altHttp, self::INBOX_ID, 'other_contact');

        $altHttp
            ->expects($this->once())
            ->method('get')
            ->with('/public/api/v1/inboxes/inbox_abc123/contacts/other_contact/conversations', [])
            ->willReturn([]);

        $altResource->list();
    }
}
