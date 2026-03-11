<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\AuditLogDTO;

class AuditLogsResource extends BaseResource
{
    /**
     * @return AuditLogDTO[]
     */
    public function list(int $page = 1): array
    {
        $data  = $this->http->get($this->basePath('audit_log'), ['page' => $page]);
        $items = $data['payload'] ?? $data;

        return AuditLogDTO::collect(is_array($items) ? $items : []);
    }
}
