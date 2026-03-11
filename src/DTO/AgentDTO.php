<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

use RamiroEstrella\ChatwootPhpSdk\Enums\AgentAvailabilityStatus;
use RamiroEstrella\ChatwootPhpSdk\Enums\AgentRole;

class AgentDTO extends BaseDTO
{
    public ?int                      $id                  = null;
    public ?int                      $account_id          = null;
    public ?string                   $name                = null;
    public ?string                   $email               = null;
    public ?string                   $display_name        = null;
    public ?AgentRole                $role                = null;
    public ?AgentAvailabilityStatus  $availability_status = null;
    public ?bool                     $auto_offline        = null;
    public ?bool                     $confirmed           = null;
    public ?string                   $thumbnail           = null;
    public ?string                   $created_at          = null;
    public ?array                    $custom_attributes   = null;
}
