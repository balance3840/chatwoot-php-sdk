<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;
use RamiroEstrella\ChatwootPhpSdk\Enums\AgentAvailabilityStatus;
use RamiroEstrella\ChatwootPhpSdk\Enums\AgentRole;

class AgentsResource extends BaseResource
{
    /**
     * @return AgentDTO[]
     */
    public function list(): array
    {
        $data = $this->http->get($this->basePath('agents'));

        return AgentDTO::collect($data);
    }

    public function create(
        string $name,
        string $email,
        AgentRole|string $role = AgentRole::Agent,
        ?AgentAvailabilityStatus $availabilityStatus = null
    ): AgentDTO {
        $params = [
            'name'  => $name,
            'email' => $email,
            'role'  => $role instanceof AgentRole ? $role->value : $role,
        ];

        if ($availabilityStatus !== null) {
            $params['availability_status'] = $availabilityStatus->value;
        }

        $data = $this->http->post($this->basePath('agents'), $params);

        return AgentDTO::fromArray($data);
    }

    public function update(
        int $agentId,
        array $params
    ): AgentDTO {
        // Coerce enum values in params if passed
        if (isset($params['role']) && $params['role'] instanceof AgentRole) {
            $params['role'] = $params['role']->value;
        }
        if (isset($params['availability_status']) && $params['availability_status'] instanceof AgentAvailabilityStatus) {
            $params['availability_status'] = $params['availability_status']->value;
        }

        $data = $this->http->put($this->basePath("agents/{$agentId}"), $this->filterParams($params));

        return AgentDTO::fromArray($data);
    }

    public function delete(int $agentId): array
    {
        return $this->http->delete($this->basePath("agents/{$agentId}"));
    }
}
