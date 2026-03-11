<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ContactLabelsResource;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class ContactLabelsResourceTest extends ResourceTestCase
{
    private ContactLabelsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new ContactLabelsResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_calls_correct_uri(): void
    {
        $this->expectGet(self::BASE . '/contacts/42/labels', ['payload' => ['vip', 'enterprise']], []);

        $result = $this->resource->list(42);

        $this->assertSame(['payload' => ['vip', 'enterprise']], $result);
    }

    public function test_update_posts_labels_array(): void
    {
        $this->expectPost(
            self::BASE . '/contacts/42/labels',
            ['labels' => ['vip', 'billing']],
            ['payload' => ['vip', 'billing']]
        );

        $result = $this->resource->update(42, ['vip', 'billing']);

        $this->assertSame(['payload' => ['vip', 'billing']], $result);
    }
}
