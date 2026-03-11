<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Exceptions;

class ValidationException extends ApiException
{
    private array $errors;

    public function __construct(string $message = '', int $code = 422, array $errors = [])
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
