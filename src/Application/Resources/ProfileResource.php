<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

/**
 * Profile Resource
 *
 * Endpoints:
 *   GET   /auth/sign_in                 - Sign in
 *   GET   /api/v1/profile               - Get profile
 *   PUT   /api/v1/profile               - Update profile
 */
class ProfileResource extends BaseResource
{
    /**
     * Get the profile of the currently authenticated user.
     */
    public function get(): array
    {
        return $this->http->get('/api/v1/profile');
    }

    /**
     * Update the profile of the currently authenticated user.
     *
     * @param array $params {
     *   @type string $name               Display name
     *   @type string $display_name       Display name override
     *   @type string $email              Email address
     *   @type string $avatar             Avatar file (base64 or URL)
     *   @type string $password           New password
     *   @type string $current_password   Current password (required when changing password)
     *   @type array  $ui_settings        UI preference settings
     *   @type string $availability       'available'|'busy'|'offline'
     *   @type bool   $auto_offline       Auto-offline when away
     * }
     */
    public function update(array $params): array
    {
        return $this->http->put('/api/v1/profile', $this->filterParams($params));
    }
}
