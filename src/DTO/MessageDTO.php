<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

use RamiroEstrella\ChatwootPhpSdk\Enums\MessageContentType;
use RamiroEstrella\ChatwootPhpSdk\Enums\MessageStatus;
use RamiroEstrella\ChatwootPhpSdk\Enums\MessageType;

class MessageDTO extends BaseDTO
{
    public ?int                $id                 = null;
    public ?string             $content            = null;
    public ?int                $account_id         = null;
    public ?int                $inbox_id           = null;
    public ?int                $conversation_id    = null;
    public ?MessageType        $message_type       = null;
    public ?MessageContentType $content_type       = null;
    public ?array              $content_attributes = null;
    public ?bool               $private            = null;
    public ?MessageStatus      $status             = null;
    public ?string             $source_id          = null;
    public ?string             $channel            = null;
    public ?array              $attachments        = null;
    public ?array              $sender             = null;
    public ?array              $template_params    = null;
    public ?int                $created_at         = null;
    public ?string             $updated_at         = null;
}
