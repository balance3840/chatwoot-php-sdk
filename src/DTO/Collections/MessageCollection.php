<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO\Collections;

use RamiroEstrella\ChatwootPhpSdk\DTO\MessageDTO;

/**
 * @extends PaginatedCollection<MessageDTO>
 */
class MessageCollection extends PaginatedCollection
{
    /** @var MessageDTO[] */
    public readonly array $items;

    /**
     * Build from Chatwoot messages list response:
     *   { "payload": [...messages] }
     */
    public static function fromResponse(array $response): static
    {
        $payload = $response['payload'] ?? $response;

        // Normalize: if the response itself is a flat array of messages
        if (isset($payload[0]) || empty($payload)) {
            $items = is_array($payload) && (empty($payload) || isset($payload[0]))
                ? MessageDTO::collect($payload)
                : [];
        } else {
            $items = MessageDTO::collect($payload);
        }

        return new static($items, count($items), 1);
    }
}
