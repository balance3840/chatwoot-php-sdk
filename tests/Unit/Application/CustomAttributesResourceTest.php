<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\CustomAttributesResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\CustomAttributeDTO;
use RamiroEstrella\ChatwootPhpSdk\Enums\CustomAttributeModel;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class CustomAttributesResourceTest extends ResourceTestCase
{
    private CustomAttributesResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new CustomAttributesResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_sends_attribute_model_param(): void
    {
        $this->expectGet(
            self::BASE . '/custom_attribute_definitions',
            [ApiResponses::customAttribute()],
            ['attribute_model' => 0]
        );

        $result = $this->resource->list(0);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(CustomAttributeDTO::class, $result[0]);
        $this->assertSame(CustomAttributeModel::Conversation, $result[0]->attribute_model);
    }

    public function test_list_contact_attributes(): void
    {
        $this->expectGet(
            self::BASE . '/custom_attribute_definitions',
            [ApiResponses::customAttribute(['attribute_model' => 1])],
            ['attribute_model' => 1]
        );

        $result = $this->resource->list(1);

        $this->assertSame(CustomAttributeModel::Contact, $result[0]->attribute_model);
    }

    public function test_create_posts_and_returns_dto(): void
    {
        $params = ['attribute_display_name' => 'Order ID', 'attribute_key' => 'order_id', 'attribute_model' => 0];
        $this->expectPost(self::BASE . '/custom_attribute_definitions', $params, ApiResponses::customAttribute());

        $attr = $this->resource->create($params);

        $this->assertInstanceOf(CustomAttributeDTO::class, $attr);
        $this->assertSame('Order ID', $attr->attribute_display_name);
    }

    public function test_show_returns_dto(): void
    {
        $this->expectGet(self::BASE . '/custom_attribute_definitions/17', ApiResponses::customAttribute(), []);

        $attr = $this->resource->show(17);

        $this->assertInstanceOf(CustomAttributeDTO::class, $attr);
        $this->assertSame(17, $attr->id);
    }

    public function test_update_patches_correct_uri(): void
    {
        $this->expectPatch(
            self::BASE . '/custom_attribute_definitions/17',
            ['attribute_display_name' => 'Updated'],
            ApiResponses::customAttribute(['attribute_display_name' => 'Updated'])
        );

        $attr = $this->resource->update(17, ['attribute_display_name' => 'Updated']);

        $this->assertInstanceOf(CustomAttributeDTO::class, $attr);
    }

    public function test_delete_calls_correct_uri(): void
    {
        $this->expectDelete(self::BASE . '/custom_attribute_definitions/17', [], []);

        $this->resource->delete(17);
    }
}
