<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\DTO;

class WebhookDTO extends BaseDTO
{
    public ?int    $id            = null;
    public ?string $url           = null;
    public ?string $name          = null;
    public ?array  $subscriptions = null;
}
