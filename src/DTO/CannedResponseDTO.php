<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

class CannedResponseDTO extends BaseDTO
{
    public ?int    $id         = null;
    public ?int    $account_id = null;
    public ?string $name       = null;
    public ?string $short_code = null;
    public ?string $content    = null;
}
