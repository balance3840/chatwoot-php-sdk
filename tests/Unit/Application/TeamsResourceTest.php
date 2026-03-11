<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\TeamsResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\TeamDTO;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class TeamsResourceTest extends ResourceTestCase
{
    private TeamsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new TeamsResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_returns_team_dtos(): void
    {
        $this->expectGet(self::BASE . '/teams', [ApiResponses::team()], []);

        $result = $this->resource->list();

        $this->assertCount(1, $result);
        $this->assertInstanceOf(TeamDTO::class, $result[0]);
    }

    public function test_create_posts_name_only(): void
    {
        $this->expectPost(self::BASE . '/teams', ['name' => 'Support'], ApiResponses::team(['name' => 'Support']));

        $team = $this->resource->create('Support');

        $this->assertInstanceOf(TeamDTO::class, $team);
        $this->assertSame('Support', $team->name);
    }

    public function test_create_includes_description_when_given(): void
    {
        $this->expectPost(
            self::BASE . '/teams',
            ['name' => 'Support', 'description' => 'Handles tickets'],
            ApiResponses::team(['description' => 'Handles tickets'])
        );

        $this->resource->create('Support', 'Handles tickets');
    }

    public function test_show_returns_team_dto(): void
    {
        $this->expectGet(self::BASE . '/teams/5', ApiResponses::team(), []);

        $team = $this->resource->show(5);

        $this->assertInstanceOf(TeamDTO::class, $team);
        $this->assertSame(5, $team->id);
    }

    public function test_update_patches_correct_uri(): void
    {
        $this->expectPatch(self::BASE . '/teams/5', ['name' => 'New Name'], ApiResponses::team(['name' => 'New Name']));

        $team = $this->resource->update(5, ['name' => 'New Name']);

        $this->assertInstanceOf(TeamDTO::class, $team);
    }

    public function test_delete_calls_correct_uri(): void
    {
        $this->expectDelete(self::BASE . '/teams/5', [], []);

        $this->resource->delete(5);
    }

    public function test_list_agents_returns_agent_dtos(): void
    {
        $this->expectGet(self::BASE . '/teams/5/team_members', [ApiResponses::agent()], []);

        $agents = $this->resource->listAgents(5);

        $this->assertCount(1, $agents);
        $this->assertInstanceOf(AgentDTO::class, $agents[0]);
    }

    public function test_add_agents_posts_user_ids(): void
    {
        $this->expectPost(self::BASE . '/teams/5/team_members', ['user_ids' => [1, 2]], [ApiResponses::agent()]);

        $agents = $this->resource->addAgents(5, [1, 2]);

        $this->assertCount(1, $agents);
    }

    public function test_update_agents_patches_user_ids(): void
    {
        $this->expectPatch(self::BASE . '/teams/5/team_members', ['user_ids' => [1]], [ApiResponses::agent()]);

        $this->resource->updateAgents(5, [1]);
    }

    public function test_remove_agents_deletes_with_user_ids(): void
    {
        $this->expectDelete(self::BASE . '/teams/5/team_members', ['user_ids' => [2]], []);

        $this->resource->removeAgents(5, [2]);
    }
}
