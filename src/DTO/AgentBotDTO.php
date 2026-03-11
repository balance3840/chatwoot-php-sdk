<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

class AgentBotDTO extends BaseDTO
{
    public ?int    $id           = null;
    public ?string $name         = null;
    public ?string $description  = null;
    public ?string $outgoing_url = null;
    public ?int    $account_id   = null;
    public ?string $bot_type     = null;
    public ?array  $bot_config   = null;
}
