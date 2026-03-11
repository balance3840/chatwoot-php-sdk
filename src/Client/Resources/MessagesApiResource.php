<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Client\Resources;
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClientInterface;

/**
 * Client Messages API Resource
 *
 * Send and receive messages from the end-user perspective.
 *
 * Endpoints:
 *   GET  /public/api/v1/inboxes/{inbox_identifier}/contacts/{contact_identifier}/conversations/{conversation_id}/messages
 *   POST /public/api/v1/inboxes/{inbox_identifier}/contacts/{contact_identifier}/conversations/{conversation_id}/messages
 */
class MessagesApiResource extends BaseClientResource
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

    private function messagesPath(int $conversationId): string
    {
        return $this->basePath(
            "contacts/{$this->contactIdentifier}/conversations/{$conversationId}/messages"
        );
    }

    /**
     * Get all messages in a conversation.
     *
     * @param int $conversationId Conversation ID
     */
    public function list(int $conversationId): array
    {
        return $this->http->get($this->messagesPath($conversationId));
    }

    /**
     * Send a message as the contact (end-user).
     *
     * @param int   $conversationId Conversation ID
     * @param array $params {
     *   @type string $content      (required) Message content
     *   @type string $message_type 'outgoing' (from contact perspective)
     * }
     */
    public function create(int $conversationId, array $params): array
    {
        return $this->http->post($this->messagesPath($conversationId), $params);
    }

    /**
     * Shorthand to send a text message as the contact.
     *
     * @param int    $conversationId Conversation ID
     * @param string $content        Message text
     */
    public function send(int $conversationId, string $content): array
    {
        return $this->create($conversationId, [
            'content'      => $content,
            'message_type' => 'outgoing',
        ]);
    }
}
