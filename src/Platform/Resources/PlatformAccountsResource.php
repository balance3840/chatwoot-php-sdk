<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Platform\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\AccountDTO;

class PlatformAccountsResource extends BasePlatformResource
{
    public function create(array $params): AccountDTO
    {
        $data = $this->httpPost($this->platformPath('accounts'), $params);

        return AccountDTO::fromArray($data);
    }

    public function show(int $accountId): AccountDTO
    {
        $data = $this->httpGet($this->platformPath("accounts/{$accountId}"));

        return AccountDTO::fromArray($data);
    }

    public function update(int $accountId, array $params): AccountDTO
    {
        $data = $this->httpPatch($this->platformPath("accounts/{$accountId}"), $this->filterParams($params));

        return AccountDTO::fromArray($data);
    }

    public function delete(int $accountId): array
    {
        return $this->httpDelete($this->platformPath("accounts/{$accountId}"));
    }
}
