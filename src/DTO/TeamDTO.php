<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

class TeamDTO extends BaseDTO
{
    public ?int    $id                 = null;
    public ?int    $account_id         = null;
    public ?string $name               = null;
    public ?string $description        = null;
    public ?bool   $allow_auto_assign  = null;
}
