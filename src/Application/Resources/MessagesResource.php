<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\MessageDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\Collections\MessageCollection;

/**
 * Messages Resource
 */
class MessagesResource extends BaseResource
{
    public function list(int $conversationId): MessageCollection
    {
        $data = $this->http->get(
            $this->basePath("conversations/{$conversationId}/messages")
        );

        return MessageCollection::fromResponse($data);
    }

    public function create(int $conversationId, array $params): MessageDTO
    {
        $data = $this->http->post(
            $this->basePath("conversations/{$conversationId}/messages"),
            $params
        );

        return MessageDTO::fromArray($data);
    }

    public function sendText(int $conversationId, string $content, bool $private = false): MessageDTO
    {
        return $this->create($conversationId, [
            'content'      => $content,
            'message_type' => 'outgoing',
            'content_type' => 'text',
            'private'      => $private,
        ]);
    }

    public function sendPrivateNote(int $conversationId, string $content): MessageDTO
    {
        return $this->sendText($conversationId, $content, true);
    }

    public function sendWhatsAppTemplate(
        int $conversationId,
        string $content,
        array $templateParams
    ): MessageDTO {
        return $this->create($conversationId, [
            'content'         => $content,
            'message_type'    => 'outgoing',
            'content_type'    => 'text',
            'template_params' => $templateParams,
        ]);
    }

    public function delete(int $conversationId, int $messageId): array
    {
        return $this->http->delete(
            $this->basePath("conversations/{$conversationId}/messages/{$messageId}")
        );
    }
}
