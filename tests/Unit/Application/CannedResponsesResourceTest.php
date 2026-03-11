<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\CannedResponsesResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\CannedResponseDTO;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class CannedResponsesResourceTest extends ResourceTestCase
{
    private CannedResponsesResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new CannedResponsesResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_without_search_sends_no_query(): void
    {
        $this->expectGet(self::BASE . '/canned_responses', [ApiResponses::cannedResponse()], []);

        $result = $this->resource->list();

        $this->assertCount(1, $result);
        $this->assertInstanceOf(CannedResponseDTO::class, $result[0]);
    }

    public function test_list_with_search_sends_search_param(): void
    {
        $this->expectGet(self::BASE . '/canned_responses', [ApiResponses::cannedResponse()], ['search' => 'hi']);

        $this->resource->list('hi');
    }

    public function test_create_posts_short_code_and_content(): void
    {
        $this->expectPost(
            self::BASE . '/canned_responses',
            ['short_code' => 'hi', 'content' => 'Hello!'],
            ApiResponses::cannedResponse()
        );

        $cr = $this->resource->create('hi', 'Hello!');

        $this->assertInstanceOf(CannedResponseDTO::class, $cr);
        $this->assertSame(9, $cr->id);
        $this->assertSame('hi', $cr->short_code);
    }

    public function test_update_puts_to_correct_uri(): void
    {
        $this->expectPut(
            self::BASE . '/canned_responses/9',
            ['content' => 'Updated content'],
            ApiResponses::cannedResponse(['content' => 'Updated content'])
        );

        $cr = $this->resource->update(9, ['content' => 'Updated content']);

        $this->assertInstanceOf(CannedResponseDTO::class, $cr);
    }

    public function test_delete_calls_correct_uri(): void
    {
        $this->expectDelete(self::BASE . '/canned_responses/9', [], []);

        $this->resource->delete(9);
    }
}
