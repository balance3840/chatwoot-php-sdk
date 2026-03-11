<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

/**
 * Conversation Assignments Resource
 *
 * Endpoints:
 *   GET    /api/v1/accounts/{account_id}/conversations/{id}/assignments      - Get assignments
 *   POST   /api/v1/accounts/{account_id}/conversations/{id}/assignments      - Assign agent
 *   DELETE /api/v1/accounts/{account_id}/conversations/{id}/assignments      - Unassign agent
 *   GET    /api/v1/accounts/{account_id}/conversations/{id}/participants      - Get participants
 *   POST   /api/v1/accounts/{account_id}/conversations/{id}/participants      - Add participant
 *   PATCH  /api/v1/accounts/{account_id}/conversations/{id}/participants      - Update participants
 *   DELETE /api/v1/accounts/{account_id}/conversations/{id}/participants      - Remove participant
 */
class ConversationAssignmentsResource extends BaseResource
{
    /**
     * Get the current assignee of a conversation.
     *
     * @param int $conversationId Conversation ID
     */
    public function getAssignee(int $conversationId): array
    {
        return $this->http->get($this->basePath("conversations/{$conversationId}/assignments"));
    }

    /**
     * Assign an agent to a conversation.
     *
     * @param int $conversationId Conversation ID
     * @param int $agentId        Agent ID to assign
     */
    public function assignAgent(int $conversationId, int $agentId): array
    {
        return $this->http->post(
            $this->basePath("conversations/{$conversationId}/assignments"),
            ['assignee_id' => $agentId]
        );
    }

    /**
     * Unassign the current agent from a conversation.
     *
     * @param int $conversationId Conversation ID
     */
    public function unassignAgent(int $conversationId): array
    {
        return $this->http->delete($this->basePath("conversations/{$conversationId}/assignments"));
    }

    /**
     * Get participants of a conversation.
     *
     * @param int $conversationId Conversation ID
     */
    public function getParticipants(int $conversationId): array
    {
        return $this->http->get($this->basePath("conversations/{$conversationId}/participants"));
    }

    /**
     * Add participants to a conversation.
     *
     * @param int   $conversationId Conversation ID
     * @param int[] $userIds        Array of user IDs to add as participants
     */
    public function addParticipants(int $conversationId, array $userIds): array
    {
        return $this->http->post(
            $this->basePath("conversations/{$conversationId}/participants"),
            ['user_ids' => $userIds]
        );
    }

    /**
     * Update the participants list of a conversation.
     *
     * @param int   $conversationId Conversation ID
     * @param int[] $userIds        New array of participant user IDs
     */
    public function updateParticipants(int $conversationId, array $userIds): array
    {
        return $this->http->patch(
            $this->basePath("conversations/{$conversationId}/participants"),
            ['user_ids' => $userIds]
        );
    }

    /**
     * Remove participants from a conversation.
     *
     * @param int   $conversationId Conversation ID
     * @param int[] $userIds        Array of user IDs to remove
     */
    public function removeParticipants(int $conversationId, array $userIds): array
    {
        return $this->http->delete(
            $this->basePath("conversations/{$conversationId}/participants"),
            ['user_ids' => $userIds]
        );
    }
}
