<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\IntegrationsResource;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class IntegrationsResourceTest extends ResourceTestCase
{
    private IntegrationsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new IntegrationsResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_calls_correct_endpoint(): void
    {
        $apps = [
            ['id' => 'slack', 'name' => 'Slack', 'hooks' => []],
            ['id' => 'dialogflow', 'name' => 'Dialogflow', 'hooks' => []],
        ];

        $this->expectGet(self::BASE . '/integrations/apps', $apps, []);

        $result = $this->resource->list();

        $this->assertCount(2, $result);
        $this->assertSame('slack', $result[0]['id']);
    }

    public function test_create_hook_posts_to_correct_endpoint(): void
    {
        $params = ['app_id' => 'slack', 'url' => 'https://hooks.slack.com/xyz', 'settings' => []];

        $this->expectPost(
            self::BASE . '/integrations/hooks',
            $params,
            ['id' => 1, 'app_id' => 'slack', 'url' => 'https://hooks.slack.com/xyz']
        );

        $result = $this->resource->createHook($params);

        $this->assertSame(1, $result['id']);
        $this->assertSame('slack', $result['app_id']);
    }

    public function test_update_hook_patches_correct_endpoint(): void
    {
        $this->expectPatch(
            self::BASE . '/integrations/hooks/1',
            ['url' => 'https://new-url.example.com/hook'],
            ['id' => 1, 'url' => 'https://new-url.example.com/hook']
        );

        $result = $this->resource->updateHook(1, ['url' => 'https://new-url.example.com/hook']);

        $this->assertSame('https://new-url.example.com/hook', $result['url']);
    }

    public function test_update_hook_filters_null_params(): void
    {
        $this->http
            ->expects($this->once())
            ->method('patch')
            ->with(
                self::BASE . '/integrations/hooks/1',
                $this->callback(fn (array $body) => !array_key_exists('settings', $body) && isset($body['url']))
            )
            ->willReturn([]);

        $this->resource->updateHook(1, ['url' => 'https://example.com', 'settings' => null]);
    }

    public function test_delete_hook_calls_correct_endpoint(): void
    {
        $this->expectDelete(self::BASE . '/integrations/hooks/1', [], []);

        $this->resource->deleteHook(1);
    }
}
