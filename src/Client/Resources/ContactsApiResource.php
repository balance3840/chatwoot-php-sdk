<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Client\Resources;

/**
 * Client Contacts API Resource
 *
 * Used to create and manage contacts from the end-user (client) perspective.
 * No agent token required — uses inbox_identifier only.
 *
 * Endpoints:
 *   POST  /public/api/v1/inboxes/{inbox_identifier}/contacts           - Create contact
 *   GET   /public/api/v1/inboxes/{inbox_identifier}/contacts/{id}      - Get contact
 *   PATCH /public/api/v1/inboxes/{inbox_identifier}/contacts/{id}      - Update contact
 */
class ContactsApiResource extends BaseClientResource
{
    /**
     * Create a new contact in the inbox.
     * Returns source_id and pubsub_token which should be cached for subsequent requests.
     *
     * @param array $params {
     *   @type string $identifier      Unique identifier from your system (e.g. user_id)
     *   @type string $identifier_hash HMAC-SHA256 of the identifier (for HMAC auth)
     *   @type string $email           Contact email
     *   @type string $name            Contact name
     *   @type string $phone_number    Phone number in E.164 format
     *   @type string $avatar          Avatar URL
     *   @type array  $custom_attributes Custom key-value data
     * }
     *
     * @return array Contains id, source_id, name, email, pubsub_token
     */
    public function create(array $params): array
    {
        return $this->http->post($this->basePath('contacts'), $params);
    }

    /**
     * Get a contact's details by their contact identifier (source_id).
     *
     * @param string $contactIdentifier The contact's source_id from the create response
     */
    public function get(string $contactIdentifier): array
    {
        return $this->http->get($this->basePath("contacts/{$contactIdentifier}"));
    }

    /**
     * Update a contact's details.
     *
     * @param string $contactIdentifier The contact's source_id
     * @param array  $params            Fields to update (name, email, phone_number, custom_attributes)
     */
    public function update(string $contactIdentifier, array $params): array
    {
        return $this->http->patch(
            $this->basePath("contacts/{$contactIdentifier}"),
            $this->filterParams($params)
        );
    }
}
