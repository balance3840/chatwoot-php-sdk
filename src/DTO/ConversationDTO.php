<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationPriority;
use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationStatus;

class ConversationDTO extends BaseDTO
{
    public ?int                  $id                    = null;
    public ?int                  $account_id            = null;
    public ?int                  $inbox_id              = null;
    public ?ConversationStatus   $status                = null;
    public ?ConversationPriority $priority              = null;
    public ?int                  $unread_count          = null;
    public ?string               $channel               = null;
    public ?array                $labels                = null;
    public ?array                $custom_attributes     = null;
    public ?array                $additional_attributes = null;

    /** @var MessageDTO[] */
    public ?array                $messages              = null;

    public ?ConversationMetaDTO  $meta                  = null;

    public ?int    $created_at           = null;
    public ?int    $updated_at           = null;
    public ?int    $waiting_since        = null;
    public ?int    $agent_last_seen_at   = null;
    public ?int    $contact_last_seen_at = null;
    public ?int    $snoozed_until        = null;

    public static function fromArray(array $data): static
    {
        $dto = parent::fromArray($data);

        if (isset($data['messages']) && is_array($data['messages'])) {
            $dto->messages = MessageDTO::collect($data['messages']);
        }

        if (isset($data['meta']) && is_array($data['meta'])) {
            $dto->meta = ConversationMetaDTO::fromArray($data['meta']);
        }

        return $dto;
    }
}
