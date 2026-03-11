<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO\Collections;

use RamiroEstrella\ChatwootPhpSdk\DTO\ConversationDTO;

/**
 * @extends PaginatedCollection<ConversationDTO>
 */
class ConversationCollection extends PaginatedCollection
{
    /** @var ConversationDTO[] */
    public readonly array $items;

    /**
     * Build from Chatwoot conversations list response:
     *   { "data": { "payload": [...], "meta": { "all_count": N, "current_page": N, ... } } }
     */
    public static function fromResponse(array $response): static
    {
        // Unwrap the outer "data" key if present
        $data        = $response['data']    ?? $response;
        $payload     = $data['payload']     ?? [];
        $meta        = $data['meta']        ?? [];
        $count       = (int) ($meta['all_count']    ?? $meta['count'] ?? count($payload));
        $currentPage = (int) ($meta['current_page'] ?? 1);

        return new static(ConversationDTO::collect($payload), $count, $currentPage);
    }
}
