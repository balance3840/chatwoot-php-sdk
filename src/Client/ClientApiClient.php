<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Client;

use RamiroEstrella\ChatwootPhpSdk\Client\Resources\ContactsApiResource;
use RamiroEstrella\ChatwootPhpSdk\Client\Resources\ConversationsApiResource;
use RamiroEstrella\ChatwootPhpSdk\Client\Resources\MessagesApiResource;
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClient;

/**
 * Client API — for building custom chat UIs for end-users.
 *
 * Authentication: Uses inbox_identifier + contact_identifier (no user token).
 * Available on both Cloud and Self-hosted installations.
 *
 * Usage:
 *   // Step 1: Create a contact (returns source_id + pubsub_token)
 *   $contact = $chatwoot->client('inbox_id')->contacts()->create([
 *       'name'  => 'Alice',
 *       'email' => 'alice@example.com',
 *   ]);
 *   $contactId = $contact['source_id'];
 *
 *   // Step 2: Create a conversation for that contact
 *   $conversation = $chatwoot->client('inbox_id')->conversations($contactId)->create();
 *
 *   // Step 3: Send a message
 *   $chatwoot->client('inbox_id')->messages($contactId)->send($conversation['id'], 'Hello!');
 */
class ClientApiClient
{
    private HttpClient $http;
    private string $inboxIdentifier;
    private ?ContactsApiResource $contactsResource = null;

    public function __construct(HttpClient $http, string $inboxIdentifier)
    {
        $this->http            = $http;
        $this->inboxIdentifier = $inboxIdentifier;
    }

    /**
     * Access the Contacts API (create/get/update contacts).
     */
    public function contacts(): ContactsApiResource
    {
        return $this->contactsResource ??= new ContactsApiResource($this->http, $this->inboxIdentifier);
    }

    /**
     * Access the Conversations API scoped to a specific contact.
     *
     * @param string $contactIdentifier The contact's source_id returned from contacts()->create()
     */
    public function conversations(string $contactIdentifier): ConversationsApiResource
    {
        return new ConversationsApiResource($this->http, $this->inboxIdentifier, $contactIdentifier);
    }

    /**
     * Access the Messages API scoped to a specific contact.
     *
     * @param string $contactIdentifier The contact's source_id
     */
    public function messages(string $contactIdentifier): MessagesApiResource
    {
        return new MessagesApiResource($this->http, $this->inboxIdentifier, $contactIdentifier);
    }
}
