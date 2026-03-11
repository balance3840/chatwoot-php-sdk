<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Platform\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;

class PlatformAccountUsersResource extends BasePlatformResource
{
    /**
     * @return AgentDTO[]
     */
    public function list(int $accountId): array
    {
        $data = $this->httpGet($this->platformPath("accounts/{$accountId}/account_users"));

        return AgentDTO::collect(is_array($data) ? $data : []);
    }

    public function create(int $accountId, int $userId, string $role = 'agent'): AgentDTO
    {
        $data = $this->httpPost($this->platformPath("accounts/{$accountId}/account_users"), [
            'user_id' => $userId,
            'role'    => $role,
        ]);

        return AgentDTO::fromArray($data);
    }

    public function delete(int $accountId, int $userId): array
    {
        return $this->httpDelete($this->platformPath("accounts/{$accountId}/account_users"), [
            'user_id' => $userId,
        ]);
    }
}
