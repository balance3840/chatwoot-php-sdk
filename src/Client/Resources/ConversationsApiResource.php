<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Client\Resources;
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClientInterface;

/**
 * Client Conversations API Resource
 *
 * Manage conversations from the end-user (client) perspective.
 *
 * Endpoints:
 *   GET  /public/api/v1/inboxes/{inbox_identifier}/contacts/{contact_identifier}/conversations     - List conversations
 *   POST /public/api/v1/inboxes/{inbox_identifier}/contacts/{contact_identifier}/conversations     - Create conversation
 *   GET  /public/api/v1/inboxes/{inbox_identifier}/contacts/{contact_identifier}/conversations/{id} - Get conversation
 *   POST /public/api/v1/inboxes/{inbox_identifier}/contacts/{contact_identifier}/conversations/{id}/resolve - Resolve
 *   POST /public/api/v1/inboxes/{inbox_identifier}/contacts/{contact_identifier}/conversations/{id}/toggle_typing - Typing
 *   POST /public/api/v1/inboxes/{inbox_identifier}/contacts/{contact_identifier}/conversations/{id}/update_last_seen - Last seen
 */
class ConversationsApiResource extends BaseClientResource
{
    private string $contactIdentifier;

    public function __construct(
        HttpClientInterface $http,
        string $inboxIdentifier,
        string $contactIdentifier
    ) {
        parent::__construct($http, $inboxIdentifier);
        $this->contactIdentifier = $contactIdentifier;
    }

    /**
     * Build the base path for conversation endpoints scoped to a contact.
     */
    private function conversationsPath(string $suffix = ''): string
    {
        $path = $this->basePath("contacts/{$this->contactIdentifier}/conversations");

        if ($suffix !== '') {
            $path .= '/' . ltrim($suffix, '/');
        }

        return $path;
    }

    /**
     * List all conversations for this contact.
     *
     * @return array Contains id, inbox_id, messages[], contact
     */
    public function list(): array
    {
        return $this->http->get($this->conversationsPath());
    }

    /**
     * Create a new conversation for this contact.
     *
     * @param array $params {
     *   @type array $custom_attributes Optional custom attributes for the conversation
     * }
     *
     * @return array Contains id, inbox_id, messages[], contact
     */
    public function create(array $params = []): array
    {
        return $this->http->post($this->conversationsPath(), $params);
    }

    /**
     * Get a single conversation by ID.
     *
     * @param int $conversationId Conversation ID
     */
    public function get(int $conversationId): array
    {
        return $this->http->get($this->conversationsPath((string) $conversationId));
    }

    /**
     * Resolve a conversation.
     *
     * @param int $conversationId Conversation ID
     */
    public function resolve(int $conversationId): array
    {
        return $this->http->post($this->conversationsPath("{$conversationId}/resolve"));
    }

    /**
     * Toggle the typing status for a conversation (show/hide typing indicator).
     *
     * @param int    $conversationId Conversation ID
     * @param string $typingStatus   'on'|'off'
     */
    public function toggleTyping(int $conversationId, string $typingStatus): array
    {
        return $this->http->post(
            $this->conversationsPath("{$conversationId}/toggle_typing"),
            ['typing_status' => $typingStatus]
        );
    }

    /**
     * Update the last seen timestamp for the contact in a conversation.
     *
     * @param int $conversationId Conversation ID
     */
    public function updateLastSeen(int $conversationId): array
    {
        return $this->http->post($this->conversationsPath("{$conversationId}/update_last_seen"));
    }
}
