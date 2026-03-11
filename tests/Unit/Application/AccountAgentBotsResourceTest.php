<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\AccountAgentBotsResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\AgentBotDTO;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class AccountAgentBotsResourceTest extends ResourceTestCase
{
    private AccountAgentBotsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new AccountAgentBotsResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_returns_bot_dtos(): void
    {
        $this->expectGet(self::BASE . '/agent_bots', [ApiResponses::agentBot()], []);

        $result = $this->resource->list();

        $this->assertCount(1, $result);
        $this->assertInstanceOf(AgentBotDTO::class, $result[0]);
        $this->assertSame('Support Bot', $result[0]->name);
    }

    public function test_create_posts_and_returns_dto(): void
    {
        $params = ['name' => 'My Bot', 'outgoing_url' => 'https://bot.example.com'];
        $this->expectPost(self::BASE . '/agent_bots', $params, ApiResponses::agentBot());

        $bot = $this->resource->create($params);

        $this->assertInstanceOf(AgentBotDTO::class, $bot);
        $this->assertSame(11, $bot->id);
    }

    public function test_show_returns_bot_dto(): void
    {
        $this->expectGet(self::BASE . '/agent_bots/11', ApiResponses::agentBot(), []);

        $bot = $this->resource->show(11);

        $this->assertInstanceOf(AgentBotDTO::class, $bot);
    }

    public function test_update_patches_correct_uri(): void
    {
        $this->expectPatch(self::BASE . '/agent_bots/11', ['name' => 'New Bot'], ApiResponses::agentBot(['name' => 'New Bot']));

        $bot = $this->resource->update(11, ['name' => 'New Bot']);

        $this->assertInstanceOf(AgentBotDTO::class, $bot);
    }

    public function test_delete_calls_correct_uri(): void
    {
        $this->expectDelete(self::BASE . '/agent_bots/11', [], []);

        $this->resource->delete(11);
    }
}
