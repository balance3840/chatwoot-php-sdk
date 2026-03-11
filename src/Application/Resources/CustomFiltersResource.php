<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

/**
 * Custom Filters Resource
 *
 * Endpoints:
 *   GET    /api/v1/accounts/{account_id}/custom_filters       - List filters
 *   POST   /api/v1/accounts/{account_id}/custom_filters       - Create filter
 *   GET    /api/v1/accounts/{account_id}/custom_filters/{id}  - Show filter
 *   PATCH  /api/v1/accounts/{account_id}/custom_filters/{id}  - Update filter
 *   DELETE /api/v1/accounts/{account_id}/custom_filters/{id}  - Delete filter
 */
class CustomFiltersResource extends BaseResource
{
    /**
     * List all custom filters.
     *
     * @param string $filterType Optional filter by type: 'conversation'|'contact'
     */
    public function list(string $filterType = ''): array
    {
        $params = $filterType !== '' ? ['filter_type' => $filterType] : [];

        return $this->http->get($this->basePath('custom_filters'), $params);
    }

    /**
     * Create a custom filter.
     *
     * @param array $params {
     *   @type string $name        Filter name (required)
     *   @type string $filter_type 'conversation'|'contact' (required)
     *   @type array  $query       Filter query definition
     * }
     */
    public function create(array $params): array
    {
        return $this->http->post($this->basePath('custom_filters'), $params);
    }

    /**
     * Show a custom filter.
     *
     * @param int $filterId Filter ID
     */
    public function show(int $filterId): array
    {
        return $this->http->get($this->basePath("custom_filters/{$filterId}"));
    }

    /**
     * Update a custom filter.
     *
     * @param int   $filterId Filter ID
     * @param array $params   Fields to update
     */
    public function update(int $filterId, array $params): array
    {
        return $this->http->patch(
            $this->basePath("custom_filters/{$filterId}"),
            $this->filterParams($params)
        );
    }

    /**
     * Delete a custom filter.
     *
     * @param int $filterId Filter ID
     */
    public function delete(int $filterId): array
    {
        return $this->http->delete($this->basePath("custom_filters/{$filterId}"));
    }
}
