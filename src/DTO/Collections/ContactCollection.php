<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO\Collections;

use RamiroEstrella\ChatwootPhpSdk\DTO\ContactDTO;

/**
 * @extends PaginatedCollection<ContactDTO>
 */
class ContactCollection extends PaginatedCollection
{
    /** @var ContactDTO[] */
    public readonly array $items;

    /**
     * Build from a Chatwoot contacts list response:
     *   { "payload": [...], "meta": { "count": N, "current_page": N } }
     */
    public static function fromResponse(array $response): static
    {
        $payload     = $response['payload'] ?? [];
        $meta        = $response['meta']    ?? [];
        $count       = (int) ($meta['count']        ?? count($payload));
        $currentPage = (int) ($meta['current_page'] ?? 1);

        return new static(ContactDTO::collect($payload), $count, $currentPage);
    }
}
