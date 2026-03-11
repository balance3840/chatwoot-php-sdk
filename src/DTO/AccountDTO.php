<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

class AccountDTO extends BaseDTO
{
    public ?int    $id                = null;
    public ?string $name              = null;
    public ?string $locale            = null;
    public ?string $timezone          = null;
    public ?string $default_language  = null;
    public ?array  $custom_attributes = null;
    public ?string $created_at        = null;
    public ?string $updated_at        = null;
}
