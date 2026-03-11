<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Enums;

enum MessageContentType: string
{
    case Text        = 'text';
    case InputEmail  = 'input_email';
    case Cards       = 'cards';
    case InputSelect = 'input_select';
    case Form        = 'form';
    case Article     = 'article';
}
