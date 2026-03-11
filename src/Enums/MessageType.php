<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Enums;

enum MessageType: int
{
    case Incoming = 0;
    case Outgoing = 1;
    case Activity = 2;
    case Template = 3;
}
