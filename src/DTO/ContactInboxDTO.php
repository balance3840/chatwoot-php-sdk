<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

/**
 * Represents the link between a Contact and an Inbox (ContactInbox record).
 * Returned by createContactInbox() and contactableInboxes().
 */
class ContactInboxDTO extends BaseDTO
{
    public ?int        $id         = null;
    public ?string     $source_id  = null;
    public ?int        $inbox_id   = null;
    public ?int        $contact_id = null;
    public ?InboxDTO   $inbox      = null;

    public static function fromArray(array $data): static
    {
        $dto = parent::fromArray($data);

        if (isset($data['inbox']) && is_array($data['inbox'])) {
            $dto->inbox = InboxDTO::fromArray($data['inbox']);
        }

        return $dto;
    }
}
