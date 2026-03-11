<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Enums;

enum AgentAvailabilityStatus: string
{
    case Available = 'available';
    case Busy      = 'busy';
    case Offline   = 'offline';
}
