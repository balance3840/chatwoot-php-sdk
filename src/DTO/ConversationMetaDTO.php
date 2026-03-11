<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

class ConversationMetaDTO extends BaseDTO
{
    public ?string $channel       = null;   // e.g. "Channel::Api", "Channel::Email"
    public ?bool   $hmac_verified = null;
    public ?array  $sender        = null;   // { id, name, thumbnail, type }
    public ?array  $assignee      = null;   // { id, name, thumbnail }
    public ?array  $team          = null;   // { id, name }
}
