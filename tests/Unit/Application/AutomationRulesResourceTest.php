<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\AutomationRulesResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\AutomationRuleDTO;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class AutomationRulesResourceTest extends ResourceTestCase
{
    private AutomationRulesResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new AutomationRulesResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_returns_rule_dtos(): void
    {
        $this->expectGet(self::BASE . '/automation_rules', ['payload' => [ApiResponses::automationRule()]], ['page' => 1]);

        $result = $this->resource->list();

        $this->assertCount(1, $result);
        $this->assertInstanceOf(AutomationRuleDTO::class, $result[0]);
        $this->assertSame('Auto assign VIP', $result[0]->name);
        $this->assertTrue($result[0]->active);
    }

    public function test_create_posts_and_unwraps_payload(): void
    {
        $params = ['name' => 'New Rule', 'event_name' => 'conversation_created', 'conditions' => [], 'actions' => []];
        $this->expectPost(self::BASE . '/automation_rules', $params, ['payload' => ApiResponses::automationRule()]);

        $rule = $this->resource->create($params);

        $this->assertInstanceOf(AutomationRuleDTO::class, $rule);
    }

    public function test_show_gets_and_unwraps_payload(): void
    {
        $this->expectGet(self::BASE . '/automation_rules/13', ['payload' => ApiResponses::automationRule()], []);

        $rule = $this->resource->show(13);

        $this->assertInstanceOf(AutomationRuleDTO::class, $rule);
        $this->assertSame(13, $rule->id);
    }

    public function test_update_patches_correct_uri(): void
    {
        $this->expectPatch(
            self::BASE . '/automation_rules/13',
            ['name' => 'Updated Rule'],
            ['payload' => ApiResponses::automationRule(['name' => 'Updated Rule'])]
        );

        $rule = $this->resource->update(13, ['name' => 'Updated Rule']);

        $this->assertInstanceOf(AutomationRuleDTO::class, $rule);
    }

    public function test_delete_calls_correct_uri(): void
    {
        $this->expectDelete(self::BASE . '/automation_rules/13', [], []);

        $this->resource->delete(13);
    }
}
