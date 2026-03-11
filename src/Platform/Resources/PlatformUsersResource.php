<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Platform\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\AgentDTO;

class PlatformUsersResource extends BasePlatformResource
{
    public function create(array $params): AgentDTO
    {
        $data = $this->httpPost($this->platformPath('users'), $params);

        return AgentDTO::fromArray($data);
    }

    public function show(int $userId): AgentDTO
    {
        $data = $this->httpGet($this->platformPath("users/{$userId}"));

        return AgentDTO::fromArray($data);
    }

    public function update(int $userId, array $params): AgentDTO
    {
        $data = $this->httpPatch($this->platformPath("users/{$userId}"), $this->filterParams($params));

        return AgentDTO::fromArray($data);
    }

    public function delete(int $userId): array
    {
        return $this->httpDelete($this->platformPath("users/{$userId}"));
    }

    public function getLoginUrl(int $userId): array
    {
        return $this->httpGet($this->platformPath("users/{$userId}/login"));
    }
}
