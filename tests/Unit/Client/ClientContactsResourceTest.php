<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Client;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RamiroEstrella\ChatwootPhpSdk\Client\Resources\ContactsApiResource;
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClient;

class ClientContactsResourceTest extends TestCase
{
    private const INBOX_ID = 'inbox_abc123';
    private const BASE     = '/public/api/v1/inboxes/inbox_abc123';

    private HttpClient&MockObject $http;
    private ContactsApiResource $resource;

    protected function setUp(): void
    {
        $this->http     = $this->createMock(HttpClient::class);
        $this->resource = new ContactsApiResource($this->http, self::INBOX_ID);
    }

    public function test_create_posts_to_inbox_contacts(): void
    {
        $params   = ['name' => 'Alice', 'email' => 'alice@example.com'];
        $response = ['id' => 1, 'source_id' => 'src_alice', 'pubsub_token' => 'tok_abc'];

        $this->http
            ->expects($this->once())
            ->method('post')
            ->with(self::BASE . '/contacts', $params)
            ->willReturn($response);

        $result = $this->resource->create($params);

        $this->assertSame('src_alice', $result['source_id']);
        $this->assertSame('tok_abc', $result['pubsub_token']);
    }

    public function test_create_with_identifier_and_custom_attributes(): void
    {
        $params = [
            'identifier'        => 'user_99',
            'name'              => 'Bob',
            'custom_attributes' => ['plan' => 'pro'],
        ];
        $response = ['id' => 2, 'source_id' => 'src_bob', 'identifier' => 'user_99'];

        $this->http
            ->expects($this->once())
            ->method('post')
            ->with(self::BASE . '/contacts', $params)
            ->willReturn($response);

        $result = $this->resource->create($params);

        $this->assertSame('src_bob', $result['source_id']);
    }

    public function test_get_fetches_contact_by_source_id(): void
    {
        $response = ['id' => 1, 'source_id' => 'src_alice', 'name' => 'Alice'];

        $this->http
            ->expects($this->once())
            ->method('get')
            ->with(self::BASE . '/contacts/src_alice', [])
            ->willReturn($response);

        $result = $this->resource->get('src_alice');

        $this->assertSame('Alice', $result['name']);
    }

    public function test_update_patches_contact_by_source_id(): void
    {
        $response = ['id' => 1, 'source_id' => 'src_alice', 'name' => 'Alice Updated'];

        $this->http
            ->expects($this->once())
            ->method('patch')
            ->with(self::BASE . '/contacts/src_alice', ['name' => 'Alice Updated'])
            ->willReturn($response);

        $result = $this->resource->update('src_alice', ['name' => 'Alice Updated']);

        $this->assertSame('Alice Updated', $result['name']);
    }

    public function test_update_filters_null_values(): void
    {
        $this->http
            ->expects($this->once())
            ->method('patch')
            ->with(
                self::BASE . '/contacts/src_alice',
                $this->callback(fn (array $body) => !array_key_exists('phone_number', $body) && isset($body['name']))
            )
            ->willReturn([]);

        $this->resource->update('src_alice', ['name' => 'Alice', 'phone_number' => null]);
    }

    public function test_base_path_uses_inbox_identifier_correctly(): void
    {
        // Verify the inbox identifier string (not integer) is used in path
        $specialInboxHttp     = $this->createMock(HttpClient::class);
        $specialInboxResource = new ContactsApiResource($specialInboxHttp, 'my-inbox-slug_XYZ');

        $specialInboxHttp
            ->expects($this->once())
            ->method('post')
            ->with('/public/api/v1/inboxes/my-inbox-slug_XYZ/contacts', $this->anything())
            ->willReturn([]);

        $specialInboxResource->create(['name' => 'Test']);
    }
}
