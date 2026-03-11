<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Platform;

use RamiroEstrella\ChatwootPhpSdk\DTO\AgentBotDTO;
use RamiroEstrella\ChatwootPhpSdk\Platform\Resources\PlatformAgentBotsResource;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;

class PlatformAgentBotsResourceTest extends PlatformResourceTestCase
{
    private PlatformAgentBotsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new PlatformAgentBotsResource($this->http, self::TOKEN);
    }

    public function test_list_returns_agent_bot_dtos(): void
    {
        $this->expectPlatform('GET', self::BASE . '/agent_bots', [ApiResponses::agentBot()]);

        $bots = $this->resource->list();

        $this->assertCount(1, $bots);
        $this->assertInstanceOf(AgentBotDTO::class, $bots[0]);
        $this->assertSame(11, $bots[0]->id);
        $this->assertSame('Support Bot', $bots[0]->name);
    }

    public function test_list_returns_empty_array_when_response_empty(): void
    {
        $this->http->method('requestWithToken')->willReturn([]);

        $bots = $this->resource->list();

        $this->assertSame([], $bots);
    }

    public function test_create_posts_params(): void
    {
        $params = ['name' => 'My Bot', 'outgoing_url' => 'https://bot.example.com/hook'];
        $this->expectPlatform('POST', self::BASE . '/agent_bots', ApiResponses::agentBot());

        $bot = $this->resource->create($params);

        $this->assertInstanceOf(AgentBotDTO::class, $bot);
    }

    public function test_show_gets_bot_by_id(): void
    {
        $this->expectPlatform('GET', self::BASE . '/agent_bots/11', ApiResponses::agentBot());

        $bot = $this->resource->show(11);

        $this->assertInstanceOf(AgentBotDTO::class, $bot);
        $this->assertSame(11, $bot->id);
    }

    public function test_update_patches_bot(): void
    {
        $this->expectPlatform('PATCH', self::BASE . '/agent_bots/11', ApiResponses::agentBot(['name' => 'Renamed Bot']));

        $bot = $this->resource->update(11, ['name' => 'Renamed Bot']);

        $this->assertInstanceOf(AgentBotDTO::class, $bot);
        $this->assertSame('Renamed Bot', $bot->name);
    }

    public function test_delete_calls_correct_uri(): void
    {
        $this->expectPlatform('DELETE', self::BASE . '/agent_bots/11', []);

        $result = $this->resource->delete(11);

        $this->assertSame([], $result);
    }
}
