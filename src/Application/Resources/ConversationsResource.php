<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\ConversationDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\ConversationCollection;
use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationPriority;
use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationStatus;

class ConversationsResource extends BaseResource
{
    public function list(array $params = []): ConversationCollection
    {
        $data = $this->http->get($this->basePath('conversations'), $this->filterParams($params));

        return ConversationCollection::fromResponse($data);
    }

    public function create(array $params): ConversationDTO
    {
        $data = $this->http->post($this->basePath('conversations'), $params);

        return ConversationDTO::fromArray($data);
    }

    public function filter(array $payload, int $page = 1): ConversationCollection
    {
        $data = $this->http->post(
            $this->basePath('conversations/filter') . "?page={$page}",
            $payload
        );

        return ConversationCollection::fromResponse($data);
    }

    public function show(int $conversationId): ConversationDTO
    {
        $data = $this->http->get($this->basePath("conversations/{$conversationId}"));

        return ConversationDTO::fromArray($data);
    }

    public function update(int $conversationId, array $params): ConversationDTO
    {
        $data = $this->http->patch(
            $this->basePath("conversations/{$conversationId}"),
            $this->filterParams($params)
        );

        return ConversationDTO::fromArray($data);
    }

    public function toggleStatus(
        int $conversationId,
        ConversationStatus|string $status,
        ?int $snoozedUntil = null
    ): ConversationDTO {
        $params = ['status' => $status instanceof ConversationStatus ? $status->value : $status];

        if ($snoozedUntil !== null) {
            $params['snoozed_until'] = $snoozedUntil;
        }

        $this->http->post($this->basePath("conversations/{$conversationId}/toggle_status"), $params);

        // Chatwoot 4.x toggleStatus returns only { payload: { success, current_status } }
        // not a full ConversationDTO — re-fetch to get the updated conversation.
        return $this->show($conversationId);
    }

    public function togglePriority(
        int $conversationId,
        ConversationPriority|string|null $priority
    ): ConversationDTO {
        $value = $priority instanceof ConversationPriority ? $priority->value : $priority;

        $this->http->post(
            $this->basePath("conversations/{$conversationId}/toggle_priority"),
            ['priority' => $value]
        );

        return $this->show($conversationId);
    }

    public function toggleTypingStatus(int $conversationId, string $typingStatus): array
    {
        return $this->http->post(
            $this->basePath("conversations/{$conversationId}/toggle_typing_status"),
            ['typing_status' => $typingStatus]
        );
    }

    public function updateCustomAttributes(int $conversationId, array $customAttributes): ConversationDTO
    {
        $data = $this->http->post(
            $this->basePath("conversations/{$conversationId}/update_custom_attributes"),
            ['custom_attributes' => $customAttributes]
        );

        return ConversationDTO::fromArray($data);
    }

    public function listLabels(int $conversationId): array
    {
        $data = $this->http->get($this->basePath("conversations/{$conversationId}/labels"));

        return $data['payload'] ?? $data;
    }

    public function addLabels(int $conversationId, array $labels): array
    {
        $data = $this->http->post(
            $this->basePath("conversations/{$conversationId}/labels"),
            ['labels' => $labels]
        );

        return $data['payload'] ?? $data;
    }

    public function reportingEvents(int $conversationId): array
    {
        return $this->http->get($this->basePath("conversations/{$conversationId}/reporting_events"));
    }

    public function counts(array $params = []): array
    {
        return $this->http->get(
            $this->basePath('conversations/counts'),
            $this->filterParams($params)
        );
    }
}
