<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

class AuditLogDTO extends BaseDTO
{
    public ?int    $id               = null;
    public ?int    $account_id       = null;
    public ?string $associated_id    = null;
    public ?string $associated_type  = null;
    public ?string $auditable_id     = null;
    public ?string $auditable_type   = null;
    public ?string $user_id          = null;
    public ?string $username         = null;
    public ?string $action           = null;
    public ?array  $audited_changes  = null;
    public ?int    $version          = null;
    public ?string $request_uuid     = null;
    public ?string $remote_address   = null;
    public ?string $created_at       = null;
}
