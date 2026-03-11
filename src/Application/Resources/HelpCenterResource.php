<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

/**
 * Help Center Resource
 *
 * Endpoints:
 *   GET    /api/v1/accounts/{account_id}/portals                               - List portals
 *   POST   /api/v1/accounts/{account_id}/portals                               - Create portal
 *   GET    /api/v1/accounts/{account_id}/portals/{portal_slug}                 - Show portal
 *   PATCH  /api/v1/accounts/{account_id}/portals/{portal_slug}                 - Update portal
 *   GET    /api/v1/accounts/{account_id}/portals/{portal_slug}/articles        - List articles
 *   POST   /api/v1/accounts/{account_id}/portals/{portal_slug}/articles        - Create article
 *   GET    /api/v1/accounts/{account_id}/portals/{portal_slug}/articles/{id}   - Show article
 *   PATCH  /api/v1/accounts/{account_id}/portals/{portal_slug}/articles/{id}   - Update article
 *   DELETE /api/v1/accounts/{account_id}/portals/{portal_slug}/articles/{id}   - Delete article
 */
class HelpCenterResource extends BaseResource
{
    // -------------------------------------------------------------------------
    // Portals
    // -------------------------------------------------------------------------

    /**
     * List all help center portals.
     */
    public function listPortals(): array
    {
        return $this->http->get($this->basePath('portals'));
    }

    /**
     * Create a help center portal.
     *
     * @param array $params {
     *   @type string $slug          Portal slug (unique identifier)
     *   @type string $name          Portal display name
     *   @type string $color         Primary brand color (hex)
     *   @type string $homepage_link External homepage URL
     *   @type array  $custom_domain Custom domain settings
     * }
     */
    public function createPortal(array $params): array
    {
        return $this->http->post($this->basePath('portals'), $params);
    }

    /**
     * Show a help center portal.
     *
     * @param string $portalSlug Portal slug
     */
    public function showPortal(string $portalSlug): array
    {
        return $this->http->get($this->basePath("portals/{$portalSlug}"));
    }

    /**
     * Update a help center portal.
     *
     * @param string $portalSlug Portal slug
     * @param array  $params     Fields to update
     */
    public function updatePortal(string $portalSlug, array $params): array
    {
        return $this->http->patch(
            $this->basePath("portals/{$portalSlug}"),
            $this->filterParams($params)
        );
    }

    // -------------------------------------------------------------------------
    // Articles
    // -------------------------------------------------------------------------

    /**
     * List all articles in a portal.
     *
     * @param string $portalSlug Portal slug
     * @param array  $params     {
     *   @type string $locale   Filter by locale
     *   @type int    $page     Page number
     * }
     */
    public function listArticles(string $portalSlug, array $params = []): array
    {
        return $this->http->get(
            $this->basePath("portals/{$portalSlug}/articles"),
            $this->filterParams($params)
        );
    }

    /**
     * Create an article in a portal.
     *
     * @param string $portalSlug Portal slug
     * @param array  $params {
     *   @type string $title    Article title (required)
     *   @type string $content  Article HTML content (required)
     *   @type string $locale   Content locale (required)
     *   @type string $status   'draft'|'published'
     *   @type int    $author_id Author (agent) ID
     * }
     */
    public function createArticle(string $portalSlug, array $params): array
    {
        return $this->http->post($this->basePath("portals/{$portalSlug}/articles"), $params);
    }

    /**
     * Show a specific article.
     *
     * @param string $portalSlug Portal slug
     * @param int    $articleId  Article ID
     */
    public function showArticle(string $portalSlug, int $articleId): array
    {
        return $this->http->get($this->basePath("portals/{$portalSlug}/articles/{$articleId}"));
    }

    /**
     * Update an article.
     *
     * @param string $portalSlug Portal slug
     * @param int    $articleId  Article ID
     * @param array  $params     Fields to update
     */
    public function updateArticle(string $portalSlug, int $articleId, array $params): array
    {
        return $this->http->patch(
            $this->basePath("portals/{$portalSlug}/articles/{$articleId}"),
            $this->filterParams($params)
        );
    }

    /**
     * Delete an article.
     *
     * @param string $portalSlug Portal slug
     * @param int    $articleId  Article ID
     */
    public function deleteArticle(string $portalSlug, int $articleId): array
    {
        return $this->http->delete($this->basePath("portals/{$portalSlug}/articles/{$articleId}"));
    }
}
