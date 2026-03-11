<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\DTO;

use PHPUnit\Framework\TestCase;
use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\ConversationDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\ContactDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\MessageDTO;
use RamiroEstrella\ChatwootPhpSdk\Enums\AgentRole;
use RamiroEstrella\ChatwootPhpSdk\Enums\AgentAvailabilityStatus;
use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationStatus;
use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationPriority;
use RamiroEstrella\ChatwootPhpSdk\Enums\MessageType;
use RamiroEstrella\ChatwootPhpSdk\Enums\MessageStatus;
use RamiroEstrella\ChatwootPhpSdk\Enums\MessageContentType;

class BaseDTOTest extends TestCase
{
    // ------------------------------------------------------------------
    // fromArray — basic hydration
    // ------------------------------------------------------------------

    public function test_from_array_hydrates_scalar_properties(): void
    {
        $dto = ContactDTO::fromArray([
            'id'    => 42,
            'name'  => 'Bob Jones',
            'email' => 'bob@example.com',
        ]);

        $this->assertSame(42, $dto->id);
        $this->assertSame('Bob Jones', $dto->name);
        $this->assertSame('bob@example.com', $dto->email);
    }

    public function test_from_array_leaves_unset_properties_null(): void
    {
        $dto = ContactDTO::fromArray(['id' => 1]);

        $this->assertSame(1, $dto->id);
        $this->assertNull($dto->name);
        $this->assertNull($dto->email);
    }

    public function test_from_array_ignores_unknown_keys(): void
    {
        $dto = ContactDTO::fromArray([
            'id'              => 1,
            'nonexistent_key' => 'should be ignored',
        ]);

        $this->assertSame(1, $dto->id);
        $this->assertFalse(property_exists($dto, 'nonexistent_key'));
    }

    public function test_from_array_handles_null_values(): void
    {
        $dto = ContactDTO::fromArray(['id' => null, 'name' => null]);

        $this->assertNull($dto->id);
        $this->assertNull($dto->name);
    }

    // ------------------------------------------------------------------
    // collect
    // ------------------------------------------------------------------

    public function test_collect_hydrates_multiple_dtos(): void
    {
        $items = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob'],
        ];

        $dtos = ContactDTO::collect($items);

        $this->assertCount(2, $dtos);
        $this->assertInstanceOf(ContactDTO::class, $dtos[0]);
        $this->assertSame(1, $dtos[0]->id);
        $this->assertSame(2, $dtos[1]->id);
    }

    public function test_collect_skips_non_array_items(): void
    {
        $dtos = ContactDTO::collect([['id' => 1], 'not_an_array', 42]);

        $this->assertCount(1, $dtos);
    }

    public function test_collect_returns_empty_array_for_empty_input(): void
    {
        $this->assertSame([], ContactDTO::collect([]));
    }

    // ------------------------------------------------------------------
    // toArray
    // ------------------------------------------------------------------

    public function test_to_array_serializes_all_properties(): void
    {
        $dto = ContactDTO::fromArray(['id' => 1, 'name' => 'Alice', 'email' => null]);
        $arr = $dto->toArray();

        $this->assertSame(1, $arr['id']);
        $this->assertSame('Alice', $arr['name']);
        $this->assertArrayHasKey('email', $arr);
        $this->assertNull($arr['email']);
    }

    public function test_to_array_excludes_null_when_requested(): void
    {
        $dto = ContactDTO::fromArray(['id' => 1, 'name' => null]);
        $arr = $dto->toArray(excludeNull: true);

        $this->assertArrayHasKey('id', $arr);
        $this->assertArrayNotHasKey('name', $arr);
    }

    // ------------------------------------------------------------------
    // Enum hydration
    // ------------------------------------------------------------------

    public function test_enum_string_is_hydrated_correctly(): void
    {
        $dto = AgentDTO::fromArray(['role' => 'agent', 'availability_status' => 'busy']);

        $this->assertSame(AgentRole::Agent, $dto->role);
        $this->assertSame(AgentAvailabilityStatus::Busy, $dto->availability_status);
    }

    public function test_enum_administrator_role(): void
    {
        $dto = AgentDTO::fromArray(['role' => 'administrator']);

        $this->assertSame(AgentRole::Administrator, $dto->role);
    }

    public function test_unknown_enum_value_results_in_null(): void
    {
        // tryFrom returns null for unknown values
        $dto = AgentDTO::fromArray(['role' => 'superadmin_unknown']);

        $this->assertNull($dto->role);
    }

    public function test_int_backed_enum_is_hydrated(): void
    {
        $dto = MessageDTO::fromArray(['message_type' => 1]);

        $this->assertSame(MessageType::Outgoing, $dto->message_type);
        $this->assertSame(1, $dto->message_type->value);
    }

    public function test_conversation_status_enum(): void
    {
        $dto = ConversationDTO::fromArray(['status' => 'resolved', 'priority' => 'high']);

        $this->assertSame(ConversationStatus::Resolved, $dto->status);
        $this->assertSame(ConversationPriority::High, $dto->priority);
    }

    public function test_message_status_and_content_type_enums(): void
    {
        $dto = MessageDTO::fromArray(['status' => 'delivered', 'content_type' => 'text']);

        $this->assertSame(MessageStatus::Delivered, $dto->status);
        $this->assertSame(MessageContentType::Text, $dto->content_type);
    }

    // ------------------------------------------------------------------
    // Type coercion — the coerceBuiltin() safety net
    // ------------------------------------------------------------------

    public function test_int_field_accepts_numeric_string(): void
    {
        $dto = ContactDTO::fromArray(['id' => '42']);

        $this->assertSame(42, $dto->id);
    }

    public function test_bool_field_accepts_integer_1(): void
    {
        $dto = ContactDTO::fromArray(['blocked' => 1]);

        $this->assertTrue($dto->blocked);
    }

    public function test_bool_field_accepts_integer_0(): void
    {
        $dto = ContactDTO::fromArray(['blocked' => 0]);

        $this->assertFalse($dto->blocked);
    }

    public function test_bool_field_accepts_string_false(): void
    {
        $dto = ContactDTO::fromArray(['blocked' => 'false']);

        $this->assertFalse($dto->blocked);
    }

    public function test_string_field_accepts_integer(): void
    {
        // channel is ?string but API might send an int — should coerce
        $dto = ConversationDTO::fromArray(['inbox_id' => '5']);

        $this->assertSame(5, $dto->inbox_id);
    }

    public function test_type_mismatch_leaves_property_null_instead_of_crashing(): void
    {
        // Passing an object where a string is expected — should not throw
        $dto = ContactDTO::fromArray(['name' => ['unexpected' => 'array_for_string_field']]);

        // The name should remain null (TypeError caught internally)
        $this->assertNull($dto->name);
    }

    // ------------------------------------------------------------------
    // Nested DTO hydration
    // ------------------------------------------------------------------

    public function test_conversation_hydrates_nested_meta_dto(): void
    {
        $dto = ConversationDTO::fromArray([
            'id'   => 1,
            'meta' => [
                'channel'       => 'Channel::Api',
                'hmac_verified' => false,
                'sender'        => ['id' => 42, 'name' => 'Bob'],
            ],
        ]);

        $this->assertNotNull($dto->meta);
        $this->assertSame('Channel::Api', $dto->meta->channel);
        $this->assertFalse($dto->meta->hmac_verified);
    }

    public function test_conversation_hydrates_nested_messages(): void
    {
        $dto = ConversationDTO::fromArray([
            'id'       => 1,
            'messages' => [
                ['id' => 200, 'content' => 'Hello', 'message_type' => 1],
                ['id' => 201, 'content' => 'World', 'message_type' => 0],
            ],
        ]);

        $this->assertCount(2, $dto->messages);
        $this->assertInstanceOf(MessageDTO::class, $dto->messages[0]);
        $this->assertSame(200, $dto->messages[0]->id);
        $this->assertSame(MessageType::Outgoing, $dto->messages[0]->message_type);
    }
}
