<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO\Collections;

/**
 * Generic wrapper for paginated Chatwoot responses.
 *
 * Most list endpoints return:
 *   { "data": { "payload": [...], "meta": { "count": N, "current_page": N } } }
 * or just:
 *   { "payload": [...], "meta": { ... } }
 *
 * @template T
 */
class PaginatedCollection
{
    /**
     * @param array $items  The hydrated DTO objects
     * @param int   $count  Total number of records (across all pages)
     * @param int   $currentPage  Current page number
     */
    public function __construct(
        /** @var array<T> */
        public readonly array $items,
        public readonly int   $count       = 0,
        public readonly int   $currentPage = 1,
    ) {}

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    /**
     * Apply a callback to each item and return a plain array of results.
     */
    public function map(callable $fn): array
    {
        return array_map($fn, $this->items);
    }

    /**
     * Filter items by a callback.
     *
     * @return static
     */
    public function filter(callable $fn): static
    {
        return new static(
            array_values(array_filter($this->items, $fn)),
            $this->count,
            $this->currentPage,
        );
    }
}
