<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\DTO\Collections;

use PHPUnit\Framework\TestCase;
use RamiroEstrella\ChatwootPhpSdk\DTO\ContactDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\ContactCollection;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\ConversationCollection;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\MessageCollection;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;

class PaginatedCollectionTest extends TestCase
{
    // ------------------------------------------------------------------
    // ContactCollection
    // ------------------------------------------------------------------

    public function test_contact_collection_from_response_hydrates_items(): void
    {
        $response   = ApiResponses::contactList(3);
        $collection = ContactCollection::fromResponse($response);

        $this->assertCount(3, $collection->items);
        $this->assertInstanceOf(ContactDTO::class, $collection->items[0]);
        $this->assertSame(3, $collection->count);
        $this->assertSame(1, $collection->currentPage);
    }

    public function test_contact_collection_is_empty_when_no_items(): void
    {
        $collection = ContactCollection::fromResponse(['payload' => [], 'meta' => ['count' => 0, 'current_page' => 1]]);

        $this->assertTrue($collection->isEmpty());
        $this->assertSame(0, $collection->count());
        $this->assertNull($collection->first());
    }

    public function test_contact_collection_first_returns_first_item(): void
    {
        $collection = ContactCollection::fromResponse(ApiResponses::contactList(2));

        $first = $collection->first();
        $this->assertInstanceOf(ContactDTO::class, $first);
        $this->assertSame(1, $first->id);
    }

    public function test_contact_collection_map_applies_callback(): void
    {
        $collection = ContactCollection::fromResponse(ApiResponses::contactList(2));
        $ids        = $collection->map(fn (ContactDTO $c) => $c->id);

        $this->assertSame([1, 2], $ids);
    }

    public function test_contact_collection_filter_returns_matching_items(): void
    {
        $response   = ApiResponses::contactList(3);
        $collection = ContactCollection::fromResponse($response);
        $filtered   = $collection->filter(fn (ContactDTO $c) => $c->id > 1);

        $this->assertCount(2, $filtered->items);
    }

    // ------------------------------------------------------------------
    // ConversationCollection
    // ------------------------------------------------------------------

    public function test_conversation_collection_unwraps_data_key(): void
    {
        $response   = ApiResponses::conversationList(2);
        $collection = ConversationCollection::fromResponse($response);

        $this->assertCount(2, $collection->items);
        $this->assertSame(2, $collection->count);
    }

    public function test_conversation_collection_handles_flat_response(): void
    {
        // Some endpoints return payload without the outer data key
        $response = [
            'payload' => [ApiResponses::conversation()],
            'meta'    => ['all_count' => 1, 'current_page' => 1],
        ];

        $collection = ConversationCollection::fromResponse($response);
        $this->assertCount(1, $collection->items);
    }

    // ------------------------------------------------------------------
    // MessageCollection
    // ------------------------------------------------------------------

    public function test_message_collection_from_response(): void
    {
        $response   = ApiResponses::messageList(3);
        $collection = MessageCollection::fromResponse($response);

        $this->assertCount(3, $collection->items);
        $this->assertFalse($collection->isEmpty());
    }

    public function test_message_collection_count_matches_items(): void
    {
        $collection = MessageCollection::fromResponse(ApiResponses::messageList(5));

        $this->assertSame(5, $collection->count());
    }
}
