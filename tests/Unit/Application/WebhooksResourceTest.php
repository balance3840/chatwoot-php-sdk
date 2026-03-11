<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\WebhooksResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\WebhookDTO;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class WebhooksResourceTest extends ResourceTestCase
{
    private WebhooksResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new WebhooksResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_returns_webhook_dtos(): void
    {
        $this->expectGet(self::BASE . '/webhooks', ['payload' => [ApiResponses::webhook()]], []);

        $result = $this->resource->list();

        $this->assertCount(1, $result);
        $this->assertInstanceOf(WebhookDTO::class, $result[0]);
        $this->assertSame(7, $result[0]->id);
    }

    public function test_create_posts_url_and_subscriptions(): void
    {
        $this->expectPost(
            self::BASE . '/webhooks',
            ['url' => 'https://example.com/hook', 'subscriptions' => ['message_created']],
            ApiResponses::webhook(['subscriptions' => ['message_created']])
        );

        $webhook = $this->resource->create('https://example.com/hook', ['message_created']);

        $this->assertInstanceOf(WebhookDTO::class, $webhook);
        $this->assertSame('https://example.com/webhook', $webhook->url);
    }

    public function test_create_includes_name_when_given(): void
    {
        $this->expectPost(
            self::BASE . '/webhooks',
            ['url' => 'https://example.com/hook', 'subscriptions' => [], 'name' => 'My Hook'],
            ApiResponses::webhook(['name' => 'My Hook'])
        );

        $webhook = $this->resource->create('https://example.com/hook', [], 'My Hook');

        $this->assertSame('My Hook', $webhook->name);
    }

    public function test_update_patches_correct_uri(): void
    {
        $this->expectPatch(
            self::BASE . '/webhooks/7',
            ['url' => 'https://new.com/hook', 'subscriptions' => ['contact_created']],
            ApiResponses::webhook(['url' => 'https://new.com/hook'])
        );

        $webhook = $this->resource->update(7, 'https://new.com/hook', ['contact_created']);

        $this->assertInstanceOf(WebhookDTO::class, $webhook);
    }

    public function test_delete_calls_correct_uri(): void
    {
        $this->expectDelete(self::BASE . '/webhooks/7', [], []);

        $this->resource->delete(7);
    }

    public function test_event_constants_have_correct_values(): void
    {
        $this->assertSame('conversation_created', WebhooksResource::EVENT_CONVERSATION_CREATED);
        $this->assertSame('message_created', WebhooksResource::EVENT_MESSAGE_CREATED);
        $this->assertSame('contact_created', WebhooksResource::EVENT_CONTACT_CREATED);
        $this->assertSame('webwidget_triggered', WebhooksResource::EVENT_WEBWIDGET_TRIGGERED);
    }
}
