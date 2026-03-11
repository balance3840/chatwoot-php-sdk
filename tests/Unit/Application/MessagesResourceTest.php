<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\MessagesResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\MessageDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\MessageCollection;
use RamiroEstrella\ChatwootPhpSdk\Enums\MessageType;
use RamiroEstrella\ChatwootPhpSdk\Enums\MessageStatus;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class MessagesResourceTest extends ResourceTestCase
{
    private MessagesResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new MessagesResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_returns_message_collection(): void
    {
        $this->expectGet(self::BASE . '/conversations/100/messages', ApiResponses::messageList(3), []);

        $result = $this->resource->list(100);

        $this->assertInstanceOf(MessageCollection::class, $result);
        $this->assertCount(3, $result->items);
        $this->assertInstanceOf(MessageDTO::class, $result->items[0]);
    }

    public function test_create_posts_to_correct_endpoint(): void
    {
        $params = ['content' => 'Hi', 'message_type' => 'outgoing', 'content_type' => 'text', 'private' => false];
        $this->expectPost(self::BASE . '/conversations/100/messages', $params, ApiResponses::message());

        $msg = $this->resource->create(100, $params);

        $this->assertInstanceOf(MessageDTO::class, $msg);
        $this->assertSame(200, $msg->id);
    }

    public function test_send_text_posts_outgoing_message(): void
    {
        $this->expectPost(
            self::BASE . '/conversations/100/messages',
            ['content' => 'Hello!', 'message_type' => 'outgoing', 'content_type' => 'text', 'private' => false],
            ApiResponses::message(['content' => 'Hello!'])
        );

        $msg = $this->resource->sendText(100, 'Hello!');

        $this->assertInstanceOf(MessageDTO::class, $msg);
        $this->assertSame('Hello!', $msg->content);
        $this->assertSame(MessageType::Outgoing, $msg->message_type);
        $this->assertSame(MessageStatus::Sent, $msg->status);
    }

    public function test_send_text_private_flag(): void
    {
        $this->expectPost(
            self::BASE . '/conversations/100/messages',
            ['content' => 'Note', 'message_type' => 'outgoing', 'content_type' => 'text', 'private' => true],
            ApiResponses::message(['content' => 'Note', 'private' => true])
        );

        $msg = $this->resource->sendText(100, 'Note', true);

        $this->assertTrue($msg->private);
    }

    public function test_send_private_note_sets_private_true(): void
    {
        $this->expectPost(
            self::BASE . '/conversations/100/messages',
            ['content' => 'Internal', 'message_type' => 'outgoing', 'content_type' => 'text', 'private' => true],
            ApiResponses::message(['private' => true])
        );

        $msg = $this->resource->sendPrivateNote(100, 'Internal');

        $this->assertTrue($msg->private);
    }

    public function test_send_whatsapp_template(): void
    {
        $templateParams = ['name' => 'order_confirmation', 'category' => 'MARKETING', 'language' => 'en', 'processed_params' => []];

        $this->expectPost(
            self::BASE . '/conversations/100/messages',
            [
                'content'         => 'Your order is confirmed',
                'message_type'    => 'outgoing',
                'content_type'    => 'text',
                'template_params' => $templateParams,
            ],
            ApiResponses::message(['template_params' => $templateParams])
        );

        $msg = $this->resource->sendWhatsAppTemplate(100, 'Your order is confirmed', $templateParams);

        $this->assertSame($templateParams, $msg->template_params);
    }

    public function test_delete_calls_correct_endpoint(): void
    {
        $this->expectDelete(self::BASE . '/conversations/100/messages/200', [], []);

        $this->resource->delete(100, 200);
    }

    public function test_message_dto_hydrates_enums(): void
    {
        $this->http->method('post')->willReturn(ApiResponses::message([
            'message_type' => 0,
            'content_type' => 'input_email',
            'status'       => 'read',
        ]));

        $msg = $this->resource->create(100, ['content' => 'hi']);

        $this->assertSame(MessageType::Incoming, $msg->message_type);
        $this->assertSame(\RamiroEstrella\ChatwootPhpSdk\Enums\MessageContentType::InputEmail, $msg->content_type);
        $this->assertSame(MessageStatus::Read, $msg->status);
    }
}
