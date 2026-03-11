<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\CustomAttributeDTO;

/**
 * Custom Attributes Resource
 * attribute_model: 0 = conversation_attribute, 1 = contact_attribute
 */
class CustomAttributesResource extends BaseResource
{
    /**
     * @return CustomAttributeDTO[]
     */
    public function list(int $attributeModel = 0): array
    {
        $data = $this->http->get($this->basePath('custom_attribute_definitions'), [
            'attribute_model' => $attributeModel,
        ]);

        return CustomAttributeDTO::collect(is_array($data) ? $data : []);
    }

    public function create(array $params): CustomAttributeDTO
    {
        $data = $this->http->post($this->basePath('custom_attribute_definitions'), $params);

        return CustomAttributeDTO::fromArray($data);
    }

    public function show(int $id): CustomAttributeDTO
    {
        $data = $this->http->get($this->basePath("custom_attribute_definitions/{$id}"));

        return CustomAttributeDTO::fromArray($data);
    }

    public function update(int $id, array $params): CustomAttributeDTO
    {
        $data = $this->http->patch(
            $this->basePath("custom_attribute_definitions/{$id}"),
            $this->filterParams($params)
        );

        return CustomAttributeDTO::fromArray($data);
    }

    public function delete(int $id): array
    {
        return $this->http->delete($this->basePath("custom_attribute_definitions/{$id}"));
    }
}
