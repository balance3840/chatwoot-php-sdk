<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ConversationAssignmentsResource;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class ConversationAssignmentsResourceTest extends ResourceTestCase
{
    private ConversationAssignmentsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new ConversationAssignmentsResource($this->http, self::ACCOUNT_ID);
    }

    public function test_get_assignee(): void
    {
        $this->expectGet(self::BASE . '/conversations/100/assignments', ['id' => 1, 'name' => 'Alice'], []);

        $result = $this->resource->getAssignee(100);

        $this->assertSame(['id' => 1, 'name' => 'Alice'], $result);
    }

    public function test_assign_agent_posts_assignee_id(): void
    {
        $this->expectPost(self::BASE . '/conversations/100/assignments', ['assignee_id' => 1], ['id' => 1]);

        $this->resource->assignAgent(100, 1);
    }

    public function test_unassign_agent_deletes(): void
    {
        $this->expectDelete(self::BASE . '/conversations/100/assignments', [], []);

        $this->resource->unassignAgent(100);
    }

    public function test_get_participants(): void
    {
        $this->expectGet(self::BASE . '/conversations/100/participants', [], []);

        $this->resource->getParticipants(100);
    }

    public function test_add_participants_posts_user_ids(): void
    {
        $this->expectPost(self::BASE . '/conversations/100/participants', ['user_ids' => [1, 2]], []);

        $this->resource->addParticipants(100, [1, 2]);
    }

    public function test_update_participants_patches_user_ids(): void
    {
        $this->expectPatch(self::BASE . '/conversations/100/participants', ['user_ids' => [1]], []);

        $this->resource->updateParticipants(100, [1]);
    }

    public function test_remove_participants_deletes_user_ids(): void
    {
        $this->expectDelete(self::BASE . '/conversations/100/participants', ['user_ids' => [2]], []);

        $this->resource->removeParticipants(100, [2]);
    }
}
