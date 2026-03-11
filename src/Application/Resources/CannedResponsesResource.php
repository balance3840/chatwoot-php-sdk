<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\CannedResponseDTO;

/**
 * Canned Responses Resource
 */
class CannedResponsesResource extends BaseResource
{
    /**
     * @return CannedResponseDTO[]
     */
    public function list(string $search = ''): array
    {
        $params = $search !== '' ? ['search' => $search] : [];
        $data   = $this->http->get($this->basePath('canned_responses'), $params);

        return CannedResponseDTO::collect(is_array($data) ? $data : []);
    }

    public function create(string $shortCode, string $content): CannedResponseDTO
    {
        $data = $this->http->post($this->basePath('canned_responses'), [
            'short_code' => $shortCode,
            'content'    => $content,
        ]);

        return CannedResponseDTO::fromArray($data);
    }

    public function update(int $id, array $params): CannedResponseDTO
    {
        $data = $this->http->put($this->basePath("canned_responses/{$id}"), $this->filterParams($params));

        return CannedResponseDTO::fromArray($data);
    }

    public function delete(int $id): array
    {
        return $this->http->delete($this->basePath("canned_responses/{$id}"));
    }
}
