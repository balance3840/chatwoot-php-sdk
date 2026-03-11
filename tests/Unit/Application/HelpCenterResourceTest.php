<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\HelpCenterResource;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class HelpCenterResourceTest extends ResourceTestCase
{
    private HelpCenterResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new HelpCenterResource($this->http, self::ACCOUNT_ID);
    }

    // ------------------------------------------------------------------
    // Portals
    // ------------------------------------------------------------------

    public function test_list_portals_calls_correct_endpoint(): void
    {
        $this->expectGet(self::BASE . '/portals', [['slug' => 'my-portal', 'name' => 'My Portal']], []);

        $result = $this->resource->listPortals();

        $this->assertIsArray($result);
        $this->assertSame('my-portal', $result[0]['slug']);
    }

    public function test_create_portal_posts_params(): void
    {
        $params = ['slug' => 'new-portal', 'name' => 'New Portal', 'color' => '#FF0000'];

        $this->expectPost(self::BASE . '/portals', $params, ['slug' => 'new-portal', 'name' => 'New Portal']);

        $result = $this->resource->createPortal($params);

        $this->assertSame('new-portal', $result['slug']);
    }

    public function test_show_portal_calls_correct_endpoint(): void
    {
        $this->expectGet(self::BASE . '/portals/my-portal', ['slug' => 'my-portal'], []);

        $result = $this->resource->showPortal('my-portal');

        $this->assertSame('my-portal', $result['slug']);
    }

    public function test_update_portal_patches_correct_endpoint(): void
    {
        $this->expectPatch(
            self::BASE . '/portals/my-portal',
            ['name' => 'Updated Portal'],
            ['slug' => 'my-portal', 'name' => 'Updated Portal']
        );

        $result = $this->resource->updatePortal('my-portal', ['name' => 'Updated Portal']);

        $this->assertSame('Updated Portal', $result['name']);
    }

    public function test_update_portal_filters_null_params(): void
    {
        $this->http
            ->expects($this->once())
            ->method('patch')
            ->with(
                self::BASE . '/portals/my-portal',
                $this->callback(fn (array $body) => !array_key_exists('color', $body) && isset($body['name']))
            )
            ->willReturn([]);

        $this->resource->updatePortal('my-portal', ['name' => 'Updated', 'color' => null]);
    }

    // ------------------------------------------------------------------
    // Articles
    // ------------------------------------------------------------------

    public function test_list_articles_calls_correct_endpoint(): void
    {
        $this->expectGet(
            self::BASE . '/portals/my-portal/articles',
            ['payload' => [['id' => 1, 'title' => 'Getting Started']]],
            []
        );

        $result = $this->resource->listArticles('my-portal');

        $this->assertArrayHasKey('payload', $result);
    }

    public function test_list_articles_passes_locale_param(): void
    {
        $this->expectGet(
            self::BASE . '/portals/my-portal/articles',
            [],
            ['locale' => 'en']
        );

        $this->resource->listArticles('my-portal', ['locale' => 'en']);
    }

    public function test_list_articles_passes_page_param(): void
    {
        $this->expectGet(
            self::BASE . '/portals/my-portal/articles',
            [],
            ['page' => 2]
        );

        $this->resource->listArticles('my-portal', ['page' => 2]);
    }

    public function test_create_article_posts_to_correct_endpoint(): void
    {
        $params = ['title' => 'New Article', 'content' => '<p>Hello</p>', 'locale' => 'en', 'status' => 'published'];

        $this->expectPost(
            self::BASE . '/portals/my-portal/articles',
            $params,
            ['id' => 5, 'title' => 'New Article', 'status' => 'published']
        );

        $result = $this->resource->createArticle('my-portal', $params);

        $this->assertSame(5, $result['id']);
        $this->assertSame('published', $result['status']);
    }

    public function test_show_article_calls_correct_endpoint(): void
    {
        $this->expectGet(self::BASE . '/portals/my-portal/articles/5', ['id' => 5, 'title' => 'Hello'], []);

        $result = $this->resource->showArticle('my-portal', 5);

        $this->assertSame(5, $result['id']);
    }

    public function test_update_article_patches_correct_endpoint(): void
    {
        $this->expectPatch(
            self::BASE . '/portals/my-portal/articles/5',
            ['title' => 'Updated Title'],
            ['id' => 5, 'title' => 'Updated Title']
        );

        $result = $this->resource->updateArticle('my-portal', 5, ['title' => 'Updated Title']);

        $this->assertSame('Updated Title', $result['title']);
    }

    public function test_delete_article_calls_correct_endpoint(): void
    {
        $this->expectDelete(self::BASE . '/portals/my-portal/articles/5', [], []);

        $this->resource->deleteArticle('my-portal', 5);
    }

    public function test_portal_slug_is_used_correctly_in_nested_article_paths(): void
    {
        // Verify slug with hyphens works in path
        $this->expectGet(self::BASE . '/portals/my-help-center-v2/articles/99', ['id' => 99], []);

        $this->resource->showArticle('my-help-center-v2', 99);
    }
}
