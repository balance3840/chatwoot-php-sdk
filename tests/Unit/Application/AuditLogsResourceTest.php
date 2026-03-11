<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Tests\Unit\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\AuditLogsResource;
use RamiroEstrella\ChatwootPhpSdk\DTO\AuditLogDTO;
use RamiroEstrella\ChatwootPhpSdk\Tests\Fixtures\ApiResponses;
use RamiroEstrella\ChatwootPhpSdk\Tests\Unit\ResourceTestCase;

class AuditLogsResourceTest extends ResourceTestCase
{
    private AuditLogsResource $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new AuditLogsResource($this->http, self::ACCOUNT_ID);
    }

    public function test_list_returns_audit_log_dtos(): void
    {
        $this->expectGet(self::BASE . '/audit_log', ['payload' => [ApiResponses::auditLog()]], ['page' => 1]);

        $result = $this->resource->list();

        $this->assertCount(1, $result);
        $this->assertInstanceOf(AuditLogDTO::class, $result[0]);
        $this->assertSame(15, $result[0]->id);
        $this->assertSame('create', $result[0]->action);
    }

    public function test_list_accepts_page_parameter(): void
    {
        $this->expectGet(self::BASE . '/audit_log', ['payload' => []], ['page' => 3]);

        $this->resource->list(3);
    }
}
