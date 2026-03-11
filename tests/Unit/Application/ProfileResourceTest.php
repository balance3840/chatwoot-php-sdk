<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ProfileResource;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class ProfileResourceTest extends ResourceTestCase
{
    private ProfileResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new ProfileResource($this->http, self::ACCOUNT_ID);
    }

    public function test_get_calls_global_profile_endpoint(): void
    {
        $profile = [
            'id'           => 1,
            'name'         => 'Alice Smith',
            'email'        => 'alice@example.com',
            'role'         => 'agent',
            'access_token' => 'tok_abc123',
        ];

        // Note: profile uses /api/v1/profile NOT the account-scoped path
        $this->http
            ->expects($this->once())
            ->method('get')
            ->with('/api/v1/profile', [])
            ->willReturn($profile);

        $result = $this->resource->get();

        $this->assertSame(1, $result['id']);
        $this->assertSame('alice@example.com', $result['email']);
        $this->assertSame('tok_abc123', $result['access_token']);
    }

    public function test_update_puts_to_global_profile_endpoint(): void
    {
        $params   = ['name' => 'Alice Updated', 'email' => 'alice.new@example.com'];
        $response = array_merge($params, ['id' => 1]);

        $this->http
            ->expects($this->once())
            ->method('put')
            ->with('/api/v1/profile', $params)
            ->willReturn($response);

        $result = $this->resource->update($params);

        $this->assertSame('Alice Updated', $result['name']);
    }

    public function test_update_filters_null_params(): void
    {
        $this->http
            ->expects($this->once())
            ->method('put')
            ->with(
                '/api/v1/profile',
                $this->callback(
                    fn (array $body) => !array_key_exists('password', $body) && isset($body['name'])
                )
            )
            ->willReturn([]);

        $this->resource->update(['name' => 'Alice', 'password' => null]);
    }

    public function test_update_can_include_availability(): void
    {
        $this->http
            ->expects($this->once())
            ->method('put')
            ->with('/api/v1/profile', ['availability' => 'busy'])
            ->willReturn(['id' => 1, 'availability_status' => 'busy']);

        $result = $this->resource->update(['availability' => 'busy']);

        $this->assertSame('busy', $result['availability_status']);
    }

    public function test_profile_path_is_not_account_scoped(): void
    {
        // Explicitly verify we are NOT hitting /api/v1/accounts/{id}/...
        $this->http
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->logicalNot($this->stringContains('/accounts/')),
                $this->anything()
            )
            ->willReturn([]);

        $this->resource->get();
    }
}
