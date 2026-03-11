<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\AgentBotDTO;

class AccountAgentBotsResource extends BaseResource
{
    /**
     * @return AgentBotDTO[]
     */
    public function list(): array
    {
        $data = $this->http->get($this->basePath('agent_bots'));

        return AgentBotDTO::collect(is_array($data) ? $data : []);
    }

    public function create(array $params): AgentBotDTO
    {
        $data = $this->http->post($this->basePath('agent_bots'), $params);

        return AgentBotDTO::fromArray($data);
    }

    public function show(int $id): AgentBotDTO
    {
        $data = $this->http->get($this->basePath("agent_bots/{$id}"));

        return AgentBotDTO::fromArray($data);
    }

    public function update(int $id, array $params): AgentBotDTO
    {
        $data = $this->http->patch($this->basePath("agent_bots/{$id}"), $this->filterParams($params));

        return AgentBotDTO::fromArray($data);
    }

    public function delete(int $id): array
    {
        return $this->http->delete($this->basePath("agent_bots/{$id}"));
    }
}
