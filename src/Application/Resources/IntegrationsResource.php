<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

/**
 * Integrations Resource
 *
 * Endpoints:
 *   GET    /api/v1/accounts/{account_id}/integrations/apps           - List all integrations
 *   POST   /api/v1/accounts/{account_id}/integrations/hooks          - Create integration hook
 *   DELETE /api/v1/accounts/{account_id}/integrations/hooks/{id}     - Delete hook
 *   PATCH  /api/v1/accounts/{account_id}/integrations/hooks/{id}     - Update hook
 */
class IntegrationsResource extends BaseResource
{
    /**
     * List all available integrations and their status.
     *
     * @return array Array of integration apps with hooks
     */
    public function list(): array
    {
        return $this->http->get($this->basePath('integrations/apps'));
    }

    /**
     * Create an integration hook.
     *
     * @param array $params {
     *   @type string $app_id     Integration app ID (required)
     *   @type string $url        Hook URL endpoint
     *   @type array  $settings   Integration-specific settings
     * }
     */
    public function createHook(array $params): array
    {
        return $this->http->post($this->basePath('integrations/hooks'), $params);
    }

    /**
     * Update an integration hook.
     *
     * @param int   $hookId Hook ID
     * @param array $params Fields to update (url, settings)
     */
    public function updateHook(int $hookId, array $params): array
    {
        return $this->http->patch(
            $this->basePath("integrations/hooks/{$hookId}"),
            $this->filterParams($params)
        );
    }

    /**
     * Delete an integration hook.
     *
     * @param int $hookId Hook ID
     */
    public function deleteHook(int $hookId): array
    {
        return $this->http->delete($this->basePath("integrations/hooks/{$hookId}"));
    }
}
