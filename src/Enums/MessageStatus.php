<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Enums;

enum MessageStatus: string
{
    case Sent      = 'sent';
    case Delivered = 'delivered';
    case Read      = 'read';
    case Failed    = 'failed';
}
