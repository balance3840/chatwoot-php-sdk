<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\AccountDTO;

class AccountResource extends BaseResource
{
    public function show(): AccountDTO
    {
        $data = $this->http->get($this->basePath(''));

        return AccountDTO::fromArray($data);
    }

    public function update(array $params): AccountDTO
    {
        $data = $this->http->patch($this->basePath(''), $this->filterParams($params));

        return AccountDTO::fromArray($data);
    }
}
