<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\AgentsResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;
use RamiroEstrella\ChatwootPhpSdk\Enums\AgentAvailabilityStatus;
use RamiroEstrella\ChatwootPhpSdk\Enums\AgentRole;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class AgentsResourceTest extends ResourceTestCase
{
    private AgentsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new AgentsResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_calls_correct_endpoint(): void
    {
        $this->expectGet(self::BASE . '/agents', [ApiResponses::agent()], []);

        $agents = $this->resource->list();

        $this->assertCount(1, $agents);
        $this->assertInstanceOf(AgentDTO::class, $agents[0]);
    }

    public function test_list_returns_empty_array_when_no_agents(): void
    {
        $this->http->method('get')->willReturn([]);

        $this->assertSame([], $this->resource->list());
    }

    public function test_create_posts_to_correct_endpoint(): void
    {
        $this->expectPost(
            self::BASE . '/agents',
            ['name' => 'Alice', 'email' => 'alice@example.com', 'role' => 'agent'],
            ApiResponses::agent()
        );

        $agent = $this->resource->create('Alice', 'alice@example.com', AgentRole::Agent);

        $this->assertInstanceOf(AgentDTO::class, $agent);
        $this->assertSame('Alice Smith', $agent->name);
        $this->assertSame(AgentRole::Agent, $agent->role);
        $this->assertSame(AgentAvailabilityStatus::Available, $agent->availability_status);
    }

    public function test_create_accepts_string_role(): void
    {
        $this->expectPost(
            self::BASE . '/agents',
            ['name' => 'Bob', 'email' => 'bob@example.com', 'role' => 'administrator'],
            ApiResponses::agent(['role' => 'administrator'])
        );

        $agent = $this->resource->create('Bob', 'bob@example.com', 'administrator');

        $this->assertSame(AgentRole::Administrator, $agent->role);
    }

    public function test_create_includes_availability_status_when_provided(): void
    {
        $this->expectPost(
            self::BASE . '/agents',
            ['name' => 'Alice', 'email' => 'alice@example.com', 'role' => 'agent', 'availability_status' => 'busy'],
            ApiResponses::agent(['availability_status' => 'busy'])
        );

        $agent = $this->resource->create('Alice', 'alice@example.com', AgentRole::Agent, AgentAvailabilityStatus::Busy);

        $this->assertSame(AgentAvailabilityStatus::Busy, $agent->availability_status);
    }

    public function test_update_puts_to_correct_endpoint(): void
    {
        $this->expectPut(
            self::BASE . '/agents/1',
            ['role' => 'administrator'],
            ApiResponses::agent(['role' => 'administrator'])
        );

        $agent = $this->resource->update(1, ['role' => 'administrator']);

        $this->assertInstanceOf(AgentDTO::class, $agent);
        $this->assertSame(AgentRole::Administrator, $agent->role);
    }

    public function test_update_coerces_enum_values_in_params(): void
    {
        $this->expectPut(
            self::BASE . '/agents/1',
            ['role' => 'administrator', 'availability_status' => 'offline'],
            ApiResponses::agent(['role' => 'administrator', 'availability_status' => 'offline'])
        );

        $agent = $this->resource->update(1, [
            'role'                => AgentRole::Administrator,
            'availability_status' => AgentAvailabilityStatus::Offline,
        ]);

        $this->assertSame(AgentAvailabilityStatus::Offline, $agent->availability_status);
    }

    public function test_delete_calls_correct_endpoint(): void
    {
        $this->expectDelete(self::BASE . '/agents/1', [], []);

        $this->resource->delete(1);
    }
}
