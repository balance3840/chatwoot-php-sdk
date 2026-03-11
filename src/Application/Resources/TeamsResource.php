<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\TeamDTO;

/**
 * Teams Resource
 */
class TeamsResource extends BaseResource
{
    /**
     * @return TeamDTO[]
     */
    public function list(): array
    {
        $data = $this->http->get($this->basePath('teams'));

        return TeamDTO::collect(is_array($data) ? $data : []);
    }

    public function create(string $name, string $description = ''): TeamDTO
    {
        $params = ['name' => $name];

        if ($description !== '') {
            $params['description'] = $description;
        }

        $data = $this->http->post($this->basePath('teams'), $params);

        return TeamDTO::fromArray($data);
    }

    public function show(int $teamId): TeamDTO
    {
        $data = $this->http->get($this->basePath("teams/{$teamId}"));

        return TeamDTO::fromArray($data);
    }

    public function update(int $teamId, array $params): TeamDTO
    {
        $data = $this->http->patch($this->basePath("teams/{$teamId}"), $this->filterParams($params));

        return TeamDTO::fromArray($data);
    }

    public function delete(int $teamId): array
    {
        return $this->http->delete($this->basePath("teams/{$teamId}"));
    }

    /**
     * @return AgentDTO[]
     */
    public function listAgents(int $teamId): array
    {
        $data = $this->http->get($this->basePath("teams/{$teamId}/team_members"));

        return AgentDTO::collect(is_array($data) ? $data : []);
    }

    /**
     * @return AgentDTO[]
     */
    public function addAgents(int $teamId, array $agentIds): array
    {
        $data = $this->http->post($this->basePath("teams/{$teamId}/team_members"), [
            'user_ids' => $agentIds,
        ]);

        return AgentDTO::collect(is_array($data) ? $data : []);
    }

    /**
     * @return AgentDTO[]
     */
    public function updateAgents(int $teamId, array $agentIds): array
    {
        $data = $this->http->patch($this->basePath("teams/{$teamId}/team_members"), [
            'user_ids' => $agentIds,
        ]);

        return AgentDTO::collect(is_array($data) ? $data : []);
    }

    public function removeAgents(int $teamId, array $agentIds): array
    {
        return $this->http->delete($this->basePath("teams/{$teamId}/team_members"), [
            'user_ids' => $agentIds,
        ]);
    }
}
