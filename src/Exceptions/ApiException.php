<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Exceptions;

use Throwable;

class ApiException extends ChatwootException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
