<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ContactsResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\ContactDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\ContactInboxDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\ContactCollection;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\ConversationCollection;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class ContactsResourceTest extends ResourceTestCase
{
    private ContactsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new ContactsResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_returns_contact_collection(): void
    {
        $this->http->method('get')->willReturn(ApiResponses::contactList(2));

        $result = $this->resource->list();

        $this->assertInstanceOf(ContactCollection::class, $result);
        $this->assertCount(2, $result->items);
        $this->assertSame(2, $result->count);
    }

    public function test_list_sends_params_as_query(): void
    {
        $this->expectGet(self::BASE . '/contacts', ApiResponses::contactList(1), ['page' => 2]);

        $this->resource->list(['page' => 2]);
    }

    public function test_create_posts_and_returns_contact_dto(): void
    {
        $params = ['name' => 'Bob', 'email' => 'bob@example.com'];
        $this->expectPost(self::BASE . '/contacts', $params, ApiResponses::contact());

        $contact = $this->resource->create($params);

        $this->assertInstanceOf(ContactDTO::class, $contact);
        $this->assertSame(42, $contact->id);
        $this->assertSame('Bob Jones', $contact->name);
        $this->assertFalse($contact->blocked);
    }

    public function test_show_gets_correct_uri(): void
    {
        $this->expectGet(self::BASE . '/contacts/42', ApiResponses::contact(), []);

        $contact = $this->resource->show(42);

        $this->assertInstanceOf(ContactDTO::class, $contact);
        $this->assertSame(42, $contact->id);
    }

    public function test_update_puts_to_correct_uri(): void
    {
        $this->expectPut(self::BASE . '/contacts/42', ['name' => 'Robert'], ApiResponses::contact(['name' => 'Robert']));

        $contact = $this->resource->update(42, ['name' => 'Robert']);

        $this->assertInstanceOf(ContactDTO::class, $contact);
    }

    public function test_delete_calls_correct_uri(): void
    {
        $this->expectDelete(self::BASE . '/contacts/42', [], []);

        $this->resource->delete(42);
    }

    public function test_conversations_returns_conversation_collection(): void
    {
        $this->http->method('get')->willReturn(ApiResponses::conversationList(1));

        $result = $this->resource->conversations(42);

        $this->assertInstanceOf(ConversationCollection::class, $result);
        $this->assertCount(1, $result->items);
    }

    public function test_conversations_calls_correct_uri(): void
    {
        $this->expectGet(self::BASE . '/contacts/42/conversations', ApiResponses::conversationList(1), []);

        $this->resource->conversations(42);
    }

    public function test_search_sends_correct_query_params(): void
    {
        $this->expectGet(self::BASE . '/contacts/search', ApiResponses::contactList(1), [
            'q'                => 'bob',
            'page'             => 1,
            'include_contacts' => true,
        ]);

        $result = $this->resource->search('bob');

        $this->assertInstanceOf(ContactCollection::class, $result);
    }

    public function test_filter_posts_payload_with_page(): void
    {
        $payload = ['payload' => [['attribute_key' => 'email', 'filter_operator' => 'contains', 'values' => ['@acme.com'], 'query_operator' => null]]];
        $this->expectPost(self::BASE . '/contacts/filter?page=1', $payload, ApiResponses::contactList(1));

        $result = $this->resource->filter($payload);

        $this->assertInstanceOf(ContactCollection::class, $result);
    }

    public function test_filter_uses_specified_page(): void
    {
        $this->expectPost(self::BASE . '/contacts/filter?page=3', [], ApiResponses::contactList(0));

        $this->resource->filter([], 3);
    }

    public function test_create_contact_inbox_posts_and_returns_dto(): void
    {
        $this->expectPost(
            self::BASE . '/contacts/42/contact_inboxes',
            ['inbox_id' => 3],
            ApiResponses::contactInbox()
        );

        $result = $this->resource->createContactInbox(42, 3);

        $this->assertInstanceOf(ContactInboxDTO::class, $result);
        $this->assertSame('src_abc123', $result->source_id);
        $this->assertSame(3, $result->inbox_id);
    }

    public function test_create_contact_inbox_includes_source_id_when_given(): void
    {
        $this->expectPost(
            self::BASE . '/contacts/42/contact_inboxes',
            ['inbox_id' => 3, 'source_id' => 'custom_src'],
            ApiResponses::contactInbox(['source_id' => 'custom_src'])
        );

        $result = $this->resource->createContactInbox(42, 3, 'custom_src');

        $this->assertSame('custom_src', $result->source_id);
    }

    public function test_contactable_inboxes_returns_array_of_dtos(): void
    {
        $this->expectGet(
            self::BASE . '/contacts/42/contactable_inboxes',
            ['payload' => [ApiResponses::contactInbox()]],
            []
        );

        $result = $this->resource->contactableInboxes(42);

        $this->assertIsArray($result);
        $this->assertInstanceOf(ContactInboxDTO::class, $result[0]);
    }

    public function test_merge_posts_parent_and_child_ids(): void
    {
        $this->expectPost(
            self::BASE . '/contacts/merge',
            ['parent_id' => 10, 'child_id' => 20],
            ApiResponses::contact(['id' => 10])
        );

        $result = $this->resource->merge(10, 20);

        $this->assertInstanceOf(ContactDTO::class, $result);
        $this->assertSame(10, $result->id);
    }
}
