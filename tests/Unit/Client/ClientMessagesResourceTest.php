<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Client;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RamiroEstrella\ChatwootPhpSdk\Client\Resources\MessagesApiResource;
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClient;

class ClientMessagesResourceTest extends TestCase
{
    private const INBOX_ID   = 'inbox_abc123';
    private const CONTACT_ID = 'src_contact_xyz';
    private const CONV_ID    = 10;
    private const MSG_BASE   = '/public/api/v1/inboxes/inbox_abc123/contacts/src_contact_xyz/conversations/10/messages';

    private HttpClient&MockObject $http;
    private MessagesApiResource $resource;

    protected function setUp(): void
    {
        $this->http     = $this->createMock(HttpClient::class);
        $this->resource = new MessagesApiResource($this->http, self::INBOX_ID, self::CONTACT_ID);
    }

    public function test_list_gets_messages_for_conversation(): void
    {
        $response = [
            ['id' => 1, 'content' => 'Hello'],
            ['id' => 2, 'content' => 'Hi there'],
        ];

        $this->http
            ->expects($this->once())
            ->method('get')
            ->with(self::MSG_BASE, [])
            ->willReturn($response);

        $result = $this->resource->list(self::CONV_ID);

        $this->assertCount(2, $result);
        $this->assertSame('Hello', $result[0]['content']);
    }

    public function test_create_posts_message_params(): void
    {
        $params   = ['content' => 'Hello!', 'message_type' => 'outgoing'];
        $response = ['id' => 100, 'content' => 'Hello!'];

        $this->http
            ->expects($this->once())
            ->method('post')
            ->with(self::MSG_BASE, $params)
            ->willReturn($response);

        $result = $this->resource->create(self::CONV_ID, $params);

        $this->assertSame(100, $result['id']);
    }

    public function test_send_shorthand_posts_outgoing_message(): void
    {
        $this->http
            ->expects($this->once())
            ->method('post')
            ->with(
                self::MSG_BASE,
                ['content' => 'Hey!', 'message_type' => 'outgoing']
            )
            ->willReturn(['id' => 101, 'content' => 'Hey!']);

        $result = $this->resource->send(self::CONV_ID, 'Hey!');

        $this->assertSame('Hey!', $result['content']);
    }

    public function test_messages_path_scopes_to_conversation_id(): void
    {
        // A different conversation ID must produce a different path
        $this->http
            ->expects($this->once())
            ->method('get')
            ->with(
                '/public/api/v1/inboxes/inbox_abc123/contacts/src_contact_xyz/conversations/99/messages',
                []
            )
            ->willReturn([]);

        $this->resource->list(99);
    }

    public function test_messages_path_scopes_to_contact_identifier(): void
    {
        $altHttp     = $this->createMock(HttpClient::class);
        $altResource = new MessagesApiResource($altHttp, self::INBOX_ID, 'other_contact');

        $altHttp
            ->expects($this->once())
            ->method('post')
            ->with(
                '/public/api/v1/inboxes/inbox_abc123/contacts/other_contact/conversations/10/messages',
                $this->anything()
            )
            ->willReturn([]);

        $altResource->send(10, 'Test');
    }
}
