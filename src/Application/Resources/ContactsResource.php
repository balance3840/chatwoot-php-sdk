<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\ContactDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\ContactInboxDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\ContactCollection;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\ConversationCollection;

/**
 * Contacts Resource
 */
class ContactsResource extends BaseResource
{
    public function list(array $params = []): ContactCollection
    {
        $data = $this->http->get($this->basePath('contacts'), $this->filterParams($params));

        return ContactCollection::fromResponse($data);
    }

    public function create(array $params): ContactDTO
    {
        $data = $this->http->post($this->basePath('contacts'), $params);

        return ContactDTO::fromArray($this->unwrapSingle($data, 'contact'));
    }

    public function show(int $contactId): ContactDTO
    {
        $data = $this->http->get($this->basePath("contacts/{$contactId}"));

        return ContactDTO::fromArray($this->unwrapSingle($data, 'contact'));
    }

    public function update(int $contactId, array $params): ContactDTO
    {
        $data = $this->http->put($this->basePath("contacts/{$contactId}"), $this->filterParams($params));

        return ContactDTO::fromArray($this->unwrapSingle($data, 'contact'));
    }

    public function delete(int $contactId): array
    {
        return $this->http->delete($this->basePath("contacts/{$contactId}"));
    }

    public function conversations(int $contactId): ConversationCollection
    {
        $data = $this->http->get($this->basePath("contacts/{$contactId}/conversations"));

        return ConversationCollection::fromResponse($data);
    }

    public function search(string $query, int $page = 1, bool $includeContacts = true): ContactCollection
    {
        $data = $this->http->get($this->basePath('contacts/search'), [
            'q'                => $query,
            'page'             => $page,
            'include_contacts' => $includeContacts,
        ]);

        return ContactCollection::fromResponse($data);
    }

    public function filter(array $payload, int $page = 1): ContactCollection
    {
        $data = $this->http->post(
            $this->basePath('contacts/filter') . "?page={$page}",
            $payload
        );

        return ContactCollection::fromResponse($data);
    }

    public function createContactInbox(int $contactId, int $inboxId, string $sourceId = ''): ContactInboxDTO
    {
        $params = ['inbox_id' => $inboxId];

        if ($sourceId !== '') {
            $params['source_id'] = $sourceId;
        }

        $data = $this->http->post($this->basePath("contacts/{$contactId}/contact_inboxes"), $params);

        return ContactInboxDTO::fromArray($data);
    }

    /**
     * @return ContactInboxDTO[]
     */
    public function contactableInboxes(int $contactId): array
    {
        $data = $this->http->get($this->basePath("contacts/{$contactId}/contactable_inboxes"));
        $items = $data['payload'] ?? $data;

        return ContactInboxDTO::collect(is_array($items) ? $items : []);
    }

    public function merge(int $parentId, int $childId): ContactDTO
    {
        $data = $this->http->post($this->basePath('contacts/merge'), [
            'parent_id' => $parentId,
            'child_id'  => $childId,
        ]);

        return ContactDTO::fromArray($this->unwrapSingle($data, 'contact'));
    }
}
