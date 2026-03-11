<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\AutomationRuleDTO;

class AutomationRulesResource extends BaseResource
{
    /**
     * @return AutomationRuleDTO[]
     */
    public function list(int $page = 1): array
    {
        $data  = $this->http->get($this->basePath('automation_rules'), ['page' => $page]);
        $items = $data['payload'] ?? $data;

        return AutomationRuleDTO::collect(is_array($items) ? $items : []);
    }

    public function create(array $params): AutomationRuleDTO
    {
        $data = $this->http->post($this->basePath('automation_rules'), $params);

        return AutomationRuleDTO::fromArray($data['payload'] ?? $data);
    }

    public function show(int $id): AutomationRuleDTO
    {
        $data = $this->http->get($this->basePath("automation_rules/{$id}"));

        return AutomationRuleDTO::fromArray($data['payload'] ?? $data);
    }

    public function update(int $id, array $params): AutomationRuleDTO
    {
        $data = $this->http->patch($this->basePath("automation_rules/{$id}"), $this->filterParams($params));

        return AutomationRuleDTO::fromArray($data['payload'] ?? $data);
    }

    public function delete(int $id): array
    {
        return $this->http->delete($this->basePath("automation_rules/{$id}"));
    }
}
