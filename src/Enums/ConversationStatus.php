<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Enums;

enum ConversationStatus: string
{
    case Open     = 'open';
    case Resolved = 'resolved';
    case Pending  = 'pending';
    case Snoozed  = 'snoozed';
}
