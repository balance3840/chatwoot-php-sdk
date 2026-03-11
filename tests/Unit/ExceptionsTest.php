<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\ApiException;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\AuthenticationException;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\ChatwootException;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\NotFoundException;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\ValidationException;

class ExceptionsTest extends TestCase
{
    public function test_chatwood_exception_is_base(): void
    {
        $e = new ApiException('error', 500);

        $this->assertInstanceOf(ChatwootException::class, $e);
        $this->assertInstanceOf(\RuntimeException::class, $e);
    }

    public function test_api_exception_message_and_code(): void
    {
        $e = new ApiException('Something went wrong', 503);

        $this->assertSame('Something went wrong', $e->getMessage());
        $this->assertSame(503, $e->getCode());
    }

    public function test_authentication_exception_extends_api_exception(): void
    {
        $e = new AuthenticationException('Unauthorized', 401);

        $this->assertInstanceOf(ApiException::class, $e);
        $this->assertSame(401, $e->getCode());
    }

    public function test_not_found_exception_extends_api_exception(): void
    {
        $e = new NotFoundException('Not found', 404);

        $this->assertInstanceOf(ApiException::class, $e);
        $this->assertSame(404, $e->getCode());
    }

    public function test_validation_exception_stores_errors(): void
    {
        $errors = ['name' => ['is required'], 'email' => ['is invalid']];
        $e      = new ValidationException('Validation failed', 422, $errors);

        $this->assertSame(422, $e->getCode());
        $this->assertSame($errors, $e->getErrors());
        $this->assertSame('Validation failed', $e->getMessage());
    }

    public function test_validation_exception_extends_api_exception(): void
    {
        $e = new ValidationException('invalid', 422, []);

        $this->assertInstanceOf(ApiException::class, $e);
    }

    public function test_validation_exception_defaults_to_empty_errors(): void
    {
        $e = new ValidationException('fail', 422);

        $this->assertSame([], $e->getErrors());
    }

    public function test_all_exceptions_are_catchable_as_chatwoot_exception(): void
    {
        $exceptions = [
            new ApiException('a', 500),
            new AuthenticationException('b', 401),
            new NotFoundException('c', 404),
            new ValidationException('d', 422, []),
        ];

        foreach ($exceptions as $e) {
            $caught = false;
            try {
                throw $e;
            } catch (ChatwootException) {
                $caught = true;
            }
            $this->assertTrue($caught, get_class($e) . ' should be catchable as ChatwootException');
        }
    }
}
