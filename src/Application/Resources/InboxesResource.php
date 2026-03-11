<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\AgentBotDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\InboxDTO;

/**
 * Inboxes Resource
 */
class InboxesResource extends BaseResource
{
    /**
     * @return InboxDTO[]
     */
    public function list(): array
    {
        $data = $this->http->get($this->basePath('inboxes'));
        $items = $data['payload'] ?? $data;

        return InboxDTO::collect(is_array($items) ? $items : []);
    }

    public function show(int $inboxId): InboxDTO
    {
        $data = $this->http->get($this->basePath("inboxes/{$inboxId}"));

        return InboxDTO::fromArray($data);
    }

    public function create(array $params): InboxDTO
    {
        $data = $this->http->post($this->basePath('inboxes'), $params);

        return InboxDTO::fromArray($data);
    }

    public function update(int $inboxId, array $params): InboxDTO
    {
        $data = $this->http->patch($this->basePath("inboxes/{$inboxId}"), $this->filterParams($params));

        return InboxDTO::fromArray($data);
    }

    public function showAgentBot(int $inboxId): ?AgentBotDTO
    {
        $data = $this->http->get($this->basePath("inboxes/{$inboxId}/agent_bot"));

        return !empty($data) ? AgentBotDTO::fromArray($data) : null;
    }

    public function setAgentBot(int $inboxId, ?int $agentBotId): array
    {
        return $this->http->post(
            $this->basePath("inboxes/{$inboxId}/set_agent_bot"),
            ['agent_bot' => $agentBotId]
        );
    }

    /**
     * @return AgentDTO[]
     */
    public function listAgents(int $inboxId): array
    {
        $data = $this->http->get($this->basePath("inbox_members/{$inboxId}"));
        $items = $data['payload'] ?? $data;

        return AgentDTO::collect(is_array($items) ? $items : []);
    }

    /**
     * @return AgentDTO[]
     */
    public function addAgents(int $inboxId, array $agentIds): array
    {
        $data = $this->http->post($this->basePath('inbox_members'), [
            'inbox_id' => $inboxId,
            'user_ids' => $agentIds,
        ]);
        $items = $data['payload'] ?? $data;

        return AgentDTO::collect(is_array($items) ? $items : []);
    }

    /**
     * @return AgentDTO[]
     */
    public function updateAgents(int $inboxId, array $agentIds): array
    {
        $data = $this->http->patch($this->basePath("inbox_members/{$inboxId}"), [
            'user_ids' => $agentIds,
        ]);
        $items = $data['payload'] ?? $data;

        return AgentDTO::collect(is_array($items) ? $items : []);
    }

    public function removeAgent(int $inboxId, int $agentId): array
    {
        return $this->http->delete($this->basePath("inbox_members/{$inboxId}"), [
            'user_id' => $agentId,
        ]);
    }
}
