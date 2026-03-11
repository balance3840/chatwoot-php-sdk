<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\CustomFiltersResource;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class CustomFiltersResourceTest extends ResourceTestCase
{
    private CustomFiltersResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new CustomFiltersResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_without_type_sends_no_params(): void
    {
        $filters = [['id' => 1, 'name' => 'Open VIP', 'filter_type' => 'conversation']];

        $this->expectGet(self::BASE . '/custom_filters', $filters, []);

        $result = $this->resource->list();

        $this->assertCount(1, $result);
        $this->assertSame('Open VIP', $result[0]['name']);
    }

    public function test_list_with_type_sends_filter_type_param(): void
    {
        $this->expectGet(self::BASE . '/custom_filters', [], ['filter_type' => 'conversation']);

        $this->resource->list('conversation');
    }

    public function test_list_with_contact_type(): void
    {
        $this->expectGet(self::BASE . '/custom_filters', [], ['filter_type' => 'contact']);

        $this->resource->list('contact');
    }

    public function test_create_posts_to_correct_endpoint(): void
    {
        $params = [
            'name'        => 'High Priority Open',
            'filter_type' => 'conversation',
            'query'       => ['payload' => []],
        ];

        $this->expectPost(
            self::BASE . '/custom_filters',
            $params,
            ['id' => 3, 'name' => 'High Priority Open', 'filter_type' => 'conversation']
        );

        $result = $this->resource->create($params);

        $this->assertSame(3, $result['id']);
        $this->assertSame('conversation', $result['filter_type']);
    }

    public function test_show_calls_correct_endpoint(): void
    {
        $this->expectGet(self::BASE . '/custom_filters/3', ['id' => 3, 'name' => 'My Filter'], []);

        $result = $this->resource->show(3);

        $this->assertSame(3, $result['id']);
    }

    public function test_update_patches_correct_endpoint(): void
    {
        $this->expectPatch(
            self::BASE . '/custom_filters/3',
            ['name' => 'Renamed Filter'],
            ['id' => 3, 'name' => 'Renamed Filter']
        );

        $result = $this->resource->update(3, ['name' => 'Renamed Filter']);

        $this->assertSame('Renamed Filter', $result['name']);
    }

    public function test_update_filters_null_params(): void
    {
        $this->http
            ->expects($this->once())
            ->method('patch')
            ->with(
                self::BASE . '/custom_filters/3',
                $this->callback(fn (array $body) => !array_key_exists('query', $body) && isset($body['name']))
            )
            ->willReturn([]);

        $this->resource->update(3, ['name' => 'Updated', 'query' => null]);
    }

    public function test_delete_calls_correct_endpoint(): void
    {
        $this->expectDelete(self::BASE . '/custom_filters/3', [], []);

        $this->resource->delete(3);
    }
}
