<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures;

/**
 * Realistic fake API response data matching Chatwoot's actual response shapes.
 */
class ApiResponses
{
    // ------------------------------------------------------------------
    // Agents
    // ------------------------------------------------------------------

    public static function agent(array $overrides = []): array
    {
        return array_merge([
            'id'                  => 1,
            'account_id'          => 1,
            'name'                => 'Alice Smith',
            'email'               => 'alice@example.com',
            'display_name'        => 'Alice',
            'role'                => 'agent',
            'availability_status' => 'available',
            'auto_offline'        => false,
            'confirmed'           => true,
            'thumbnail'           => 'https://example.com/avatar.png',
            'created_at'          => '2024-01-01T00:00:00.000Z',
            'custom_attributes'   => [],
        ], $overrides);
    }

    // ------------------------------------------------------------------
    // Contacts
    // ------------------------------------------------------------------

    public static function contact(array $overrides = []): array
    {
        return array_merge([
            'id'                    => 42,
            'name'                  => 'Bob Jones',
            'email'                 => 'bob@example.com',
            'phone_number'          => '+1234567890',
            'identifier'            => 'user_42',
            'thumbnail'             => 'https://example.com/avatar.png',
            'location'              => 'New York',
            'blocked'               => false,
            'created_at'            => '2024-01-01T00:00:00.000Z',
            'updated_at'            => '2024-06-01T00:00:00.000Z',
            'last_activity_at'      => '2024-06-01T12:00:00.000Z',
            'additional_attributes' => [],
            'custom_attributes'     => [],
            'previous_identifiers'  => [],
        ], $overrides);
    }

    public static function contactList(int $count = 2): array
    {
        $items = array_map(
            fn ($i) => self::contact(['id' => $i, 'name' => "Contact {$i}"]),
            range(1, $count)
        );

        return [
            'payload' => $items,
            'meta'    => ['count' => $count, 'current_page' => 1],
        ];
    }

    public static function contactInbox(array $overrides = []): array
    {
        return array_merge([
            'id'         => 10,
            'source_id'  => 'src_abc123',
            'inbox_id'   => 3,
            'contact_id' => 42,
            'inbox'      => self::inbox(),
        ], $overrides);
    }

    // ------------------------------------------------------------------
    // Conversations
    // ------------------------------------------------------------------

    public static function conversation(array $overrides = []): array
    {
        return array_merge([
            'id'         => 100,
            'account_id' => 1,
            'inbox_id'   => 3,
            'status'     => 'open',
            'priority'   => 'high',
            'unread_count' => 2,
            'channel'    => 'Channel::Api',
            'labels'     => ['vip'],
            'custom_attributes'     => [],
            'additional_attributes' => [],
            'messages'   => [],
            'meta'       => [
                'channel'       => 'Channel::Api',
                'hmac_verified' => false,
                'sender'        => ['id' => 42, 'name' => 'Bob Jones', 'thumbnail' => '', 'type' => 'contact'],
                'assignee'      => ['id' => 1,  'name' => 'Alice Smith', 'thumbnail' => ''],
                'team'          => null,
            ],
            'created_at'           => 1704067200,
            'updated_at'           => 1704067200,
            'waiting_since'        => null,
            'agent_last_seen_at'   => null,
            'contact_last_seen_at' => null,
            'snoozed_until'        => null,
        ], $overrides);
    }

    public static function conversationList(int $count = 2): array
    {
        $items = array_map(
            fn ($i) => self::conversation(['id' => $i]),
            range(1, $count)
        );

        return [
            'data' => [
                'payload' => $items,
                'meta'    => ['all_count' => $count, 'current_page' => 1],
            ],
        ];
    }

    // ------------------------------------------------------------------
    // Messages
    // ------------------------------------------------------------------

    public static function message(array $overrides = []): array
    {
        return array_merge([
            'id'                 => 200,
            'content'            => 'Hello there!',
            'account_id'         => 1,
            'inbox_id'           => 3,
            'conversation_id'    => 100,
            'message_type'       => 1,
            'content_type'       => 'text',
            'content_attributes' => [],
            'private'            => false,
            'status'             => 'sent',
            'source_id'          => null,
            'channel'            => null,
            'attachments'        => [],
            'sender'             => ['id' => 1, 'name' => 'Alice Smith'],
            'template_params'    => null,
            'created_at'         => 1704067200,
            'updated_at'         => '2024-01-01T00:00:00.000Z',
        ], $overrides);
    }

    public static function messageList(int $count = 2): array
    {
        return [
            'payload' => array_map(
                fn ($i) => self::message(['id' => $i]),
                range(1, $count)
            ),
        ];
    }

    // ------------------------------------------------------------------
    // Inboxes
    // ------------------------------------------------------------------

    public static function inbox(array $overrides = []): array
    {
        return array_merge([
            'id'                     => 3,
            'account_id'             => 1,
            'name'                   => 'API Inbox',
            'channel_type'           => 'Channel::Api',
            'email'                  => null,
            'avatar_url'             => null,
            'widget_color'           => '#1F93FF',
            'website_url'            => null,
            'welcome_title'          => 'Welcome',
            'welcome_tagline'        => 'How can we help?',
            'email_enabled'          => false,
            'enable_auto_assignment' => true,
            'enable_email_collect'   => false,
            'csat_survey_enabled'    => false,
            'show_response_time'     => false,
            'out_of_office_message'  => '',
            'timezone'               => 'UTC',
            'working_hours_enabled'  => false,
            'working_hours'          => [],
            'custom_attributes'      => [],
        ], $overrides);
    }

    public static function inboxList(int $count = 2): array
    {
        return [
            'payload' => array_map(
                fn ($i) => self::inbox(['id' => $i, 'name' => "Inbox {$i}"]),
                range(1, $count)
            ),
        ];
    }

    // ------------------------------------------------------------------
    // Teams
    // ------------------------------------------------------------------

    public static function team(array $overrides = []): array
    {
        return array_merge([
            'id'                => 5,
            'account_id'        => 1,
            'name'              => 'Support Team',
            'description'       => 'Handles all support',
            'allow_auto_assign' => true,
        ], $overrides);
    }

    // ------------------------------------------------------------------
    // Webhooks
    // ------------------------------------------------------------------

    public static function webhook(array $overrides = []): array
    {
        return array_merge([
            'id'            => 7,
            'url'           => 'https://example.com/webhook',
            'name'          => 'My Webhook',
            'subscriptions' => ['conversation_created', 'message_created'],
        ], $overrides);
    }

    // ------------------------------------------------------------------
    // Canned Responses
    // ------------------------------------------------------------------

    public static function cannedResponse(array $overrides = []): array
    {
        return array_merge([
            'id'         => 9,
            'account_id' => 1,
            'name'       => 'Greeting',
            'short_code' => 'hi',
            'content'    => 'Hello! How can I help you today?',
        ], $overrides);
    }

    // ------------------------------------------------------------------
    // Account
    // ------------------------------------------------------------------

    public static function account(array $overrides = []): array
    {
        return array_merge([
            'id'               => 1,
            'name'             => 'ACME Support',
            'locale'           => 'en',
            'timezone'         => 'UTC',
            'default_language' => 'en',
            'custom_attributes'=> [],
            'created_at'       => '2024-01-01T00:00:00.000Z',
            'updated_at'       => '2024-01-01T00:00:00.000Z',
        ], $overrides);
    }

    // ------------------------------------------------------------------
    // Agent Bot
    // ------------------------------------------------------------------

    public static function agentBot(array $overrides = []): array
    {
        return array_merge([
            'id'           => 11,
            'name'         => 'Support Bot',
            'description'  => 'Automated bot',
            'outgoing_url' => 'https://bot.example.com/webhook',
            'account_id'   => 1,
            'bot_type'     => 'agent_bot',
            'bot_config'   => [],
        ], $overrides);
    }

    // ------------------------------------------------------------------
    // Automation Rule
    // ------------------------------------------------------------------

    public static function automationRule(array $overrides = []): array
    {
        return array_merge([
            'id'          => 13,
            'account_id'  => 1,
            'name'        => 'Auto assign VIP',
            'description' => '',
            'event_name'  => 'conversation_created',
            'active'      => true,
            'conditions'  => [],
            'actions'     => [],
            'created_at'  => '2024-01-01T00:00:00.000Z',
            'updated_at'  => '2024-01-01T00:00:00.000Z',
        ], $overrides);
    }

    // ------------------------------------------------------------------
    // Audit Log
    // ------------------------------------------------------------------

    public static function auditLog(array $overrides = []): array
    {
        return array_merge([
            'id'               => 15,
            'account_id'       => 1,
            'associated_id'    => '1',
            'associated_type'  => 'Account',
            'auditable_id'     => '42',
            'auditable_type'   => 'Contact',
            'user_id'          => '1',
            'username'         => 'alice@example.com',
            'action'           => 'create',
            'audited_changes'  => [],
            'version'          => 1,
            'request_uuid'     => 'uuid-abc-123',
            'remote_address'   => '127.0.0.1',
            'created_at'       => '2024-01-01T00:00:00.000Z',
        ], $overrides);
    }

    // ------------------------------------------------------------------
    // Custom Attribute
    // ------------------------------------------------------------------

    public static function customAttribute(array $overrides = []): array
    {
        return array_merge([
            'id'                       => 17,
            'account_id'               => 1,
            'attribute_display_name'   => 'Order ID',
            'attribute_key'            => 'order_id',
            'attribute_model'          => 0,
            'attribute_display_type'   => 1,
            'created_at'               => '2024-01-01T00:00:00.000Z',
            'updated_at'               => '2024-01-01T00:00:00.000Z',
            'regex_pattern'            => null,
            'regex_cue'                => null,
            'default_value'            => null,
        ], $overrides);
    }
}
