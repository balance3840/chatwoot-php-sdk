<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

class AutomationRuleDTO extends BaseDTO
{
    public ?int    $id          = null;
    public ?int    $account_id  = null;
    public ?string $name        = null;
    public ?string $description = null;
    public ?string $event_name  = null;
    public ?bool   $active      = null;
    public ?array  $conditions  = null;
    public ?array  $actions     = null;
    public ?string $created_at  = null;
    public ?string $updated_at  = null;
}
