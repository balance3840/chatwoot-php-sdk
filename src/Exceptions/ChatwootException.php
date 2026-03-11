<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Exceptions;

use RuntimeException;
use Throwable;

class ChatwootException extends RuntimeException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
