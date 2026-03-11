<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Enums;

enum AgentRole: string
{
    case Agent         = 'agent';
    case Administrator = 'administrator';
}
