<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\InboxesResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\AgentBotDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;
use RamiroEstrella\ChatwootPhpSdk\DTO\InboxDTO;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class InboxesResourceTest extends ResourceTestCase
{
    private InboxesResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new InboxesResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_returns_inbox_dtos(): void
    {
        $this->expectGet(self::BASE . '/inboxes', ApiResponses::inboxList(2), []);

        $result = $this->resource->list();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(InboxDTO::class, $result[0]);
    }

    public function test_list_handles_payload_wrapper(): void
    {
        $this->http->method('get')->willReturn(['payload' => [ApiResponses::inbox()]]);

        $result = $this->resource->list();

        $this->assertCount(1, $result);
    }

    public function test_show_returns_inbox_dto(): void
    {
        $this->expectGet(self::BASE . '/inboxes/3', ApiResponses::inbox(), []);

        $inbox = $this->resource->show(3);

        $this->assertInstanceOf(InboxDTO::class, $inbox);
        $this->assertSame(3, $inbox->id);
        $this->assertSame('API Inbox', $inbox->name);
        $this->assertSame('Channel::Api', $inbox->channel_type);
    }

    public function test_create_posts_and_returns_inbox_dto(): void
    {
        $params = ['name' => 'New Inbox', 'channel_type' => 'Channel::Api'];
        $this->expectPost(self::BASE . '/inboxes', $params, ApiResponses::inbox(['name' => 'New Inbox']));

        $inbox = $this->resource->create($params);

        $this->assertInstanceOf(InboxDTO::class, $inbox);
        $this->assertSame('New Inbox', $inbox->name);
    }

    public function test_update_patches_and_returns_inbox_dto(): void
    {
        $this->expectPatch(self::BASE . '/inboxes/3', ['name' => 'Updated'], ApiResponses::inbox(['name' => 'Updated']));

        $inbox = $this->resource->update(3, ['name' => 'Updated']);

        $this->assertInstanceOf(InboxDTO::class, $inbox);
    }

    public function test_show_agent_bot_returns_dto(): void
    {
        $this->expectGet(self::BASE . '/inboxes/3/agent_bot', ApiResponses::agentBot(), []);

        $bot = $this->resource->showAgentBot(3);

        $this->assertInstanceOf(AgentBotDTO::class, $bot);
        $this->assertSame(11, $bot->id);
    }

    public function test_show_agent_bot_returns_null_when_empty(): void
    {
        $this->http->method('get')->willReturn([]);

        $bot = $this->resource->showAgentBot(3);

        $this->assertNull($bot);
    }

    public function test_set_agent_bot_posts_correct_payload(): void
    {
        $this->expectPost(self::BASE . '/inboxes/3/set_agent_bot', ['agent_bot' => 11], []);

        $this->resource->setAgentBot(3, 11);
    }

    public function test_set_agent_bot_null_removes_bot(): void
    {
        $this->expectPost(self::BASE . '/inboxes/3/set_agent_bot', ['agent_bot' => null], []);

        $this->resource->setAgentBot(3, null);
    }

    public function test_list_agents_returns_agent_dtos(): void
    {
        $this->expectGet(self::BASE . '/inbox_members/3', ['payload' => [ApiResponses::agent()]], []);

        $agents = $this->resource->listAgents(3);

        $this->assertCount(1, $agents);
        $this->assertInstanceOf(AgentDTO::class, $agents[0]);
    }

    public function test_add_agents_posts_correct_payload(): void
    {
        $this->expectPost(
            self::BASE . '/inbox_members',
            ['inbox_id' => 3, 'user_ids' => [1, 2]],
            ['payload' => [ApiResponses::agent()]]
        );

        $agents = $this->resource->addAgents(3, [1, 2]);

        $this->assertInstanceOf(AgentDTO::class, $agents[0]);
    }

    public function test_update_agents_patches_correct_payload(): void
    {
        $this->expectPatch(
            self::BASE . '/inbox_members/3',
            ['user_ids' => [1]],
            ['payload' => [ApiResponses::agent()]]
        );

        $agents = $this->resource->updateAgents(3, [1]);

        $this->assertCount(1, $agents);
    }

    public function test_remove_agent_calls_delete(): void
    {
        $this->expectDelete(self::BASE . '/inbox_members/3', ['user_id' => 2], []);

        $this->resource->removeAgent(3, 2);
    }
}
