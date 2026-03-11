<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Platform\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\AgentBotDTO;

class PlatformAgentBotsResource extends BasePlatformResource
{
    /**
     * @return AgentBotDTO[]
     */
    public function list(): array
    {
        $data = $this->httpGet($this->platformPath('agent_bots'));

        return AgentBotDTO::collect(is_array($data) ? $data : []);
    }

    public function create(array $params): AgentBotDTO
    {
        $data = $this->httpPost($this->platformPath('agent_bots'), $params);

        return AgentBotDTO::fromArray($data);
    }

    public function show(int $id): AgentBotDTO
    {
        $data = $this->httpGet($this->platformPath("agent_bots/{$id}"));

        return AgentBotDTO::fromArray($data);
    }

    public function update(int $id, array $params): AgentBotDTO
    {
        $data = $this->httpPatch($this->platformPath("agent_bots/{$id}"), $this->filterParams($params));

        return AgentBotDTO::fromArray($data);
    }

    public function delete(int $id): array
    {
        return $this->httpDelete($this->platformPath("agent_bots/{$id}"));
    }
}
