<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

class ContactDTO extends BaseDTO
{
    public ?int    $id                    = null;
    public ?string $name                  = null;
    public ?string $email                 = null;
    public ?string $phone_number          = null;
    public ?string $identifier            = null;
    public ?string $thumbnail             = null;
    public ?string $avatar                = null;
    public ?string $location              = null;
    public ?bool   $blocked               = null;
    public ?string $created_at            = null;
    public ?string $updated_at            = null;
    public ?string $last_activity_at      = null;
    public ?array  $additional_attributes = null;
    public ?array  $custom_attributes     = null;
    public ?array  $previous_identifiers  = null;
}
