<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

/**
 * Contact Labels Resource
 *
 * Endpoints:
 *   GET  /api/v1/accounts/{account_id}/contacts/{id}/labels  - List contact labels
 *   POST /api/v1/accounts/{account_id}/contacts/{id}/labels  - Update contact labels
 */
class ContactLabelsResource extends BaseResource
{
    /**
     * Get labels assigned to a contact.
     *
     * @param int $contactId Contact ID
     */
    public function list(int $contactId): array
    {
        return $this->http->get($this->basePath("contacts/{$contactId}/labels"));
    }

    /**
     * Update (overwrite) labels on a contact.
     *
     * @param int      $contactId Contact ID
     * @param string[] $labels    Array of label strings
     */
    public function update(int $contactId, array $labels): array
    {
        return $this->http->post($this->basePath("contacts/{$contactId}/labels"), [
            'labels' => $labels,
        ]);
    }
}
