<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

use RamiroEstrella\ChatwootPhpSdk\DTO\WebhookDTO;

/**
 * Webhooks Resource
 */
class WebhooksResource extends BaseResource
{
    public const EVENT_CONVERSATION_CREATED        = 'conversation_created';
    public const EVENT_CONVERSATION_STATUS_CHANGED = 'conversation_status_changed';
    public const EVENT_CONVERSATION_UPDATED        = 'conversation_updated';
    public const EVENT_CONTACT_CREATED             = 'contact_created';
    public const EVENT_CONTACT_UPDATED             = 'contact_updated';
    public const EVENT_MESSAGE_CREATED             = 'message_created';
    public const EVENT_MESSAGE_UPDATED             = 'message_updated';
    public const EVENT_WEBWIDGET_TRIGGERED         = 'webwidget_triggered';

    /**
     * @return WebhookDTO[]
     */
    public function list(): array
    {
        $data = $this->http->get($this->basePath('webhooks'));

        // { "payload": { "webhooks": [...] } }
        $items = $data['payload']['webhooks']
            ?? $data['payload']
            ?? $data;

        return WebhookDTO::collect(is_array($items) ? $items : []);
    }

    public function create(string $url, array $subscriptions, string $name = ''): WebhookDTO
    {
        $params = ['url' => $url, 'subscriptions' => $subscriptions];

        if ($name !== '') {
            $params['name'] = $name;
        }

        $data = $this->http->post($this->basePath('webhooks'), $params);

        return WebhookDTO::fromArray($this->unwrapSingle($data, 'webhook'));
    }

    public function update(int $webhookId, string $url, array $subscriptions, string $name = ''): WebhookDTO
    {
        $params = ['url' => $url, 'subscriptions' => $subscriptions];

        if ($name !== '') {
            $params['name'] = $name;
        }

        $data = $this->http->patch($this->basePath("webhooks/{$webhookId}"), $params);

        return WebhookDTO::fromArray($this->unwrapSingle($data, 'webhook'));
    }

    public function delete(int $webhookId): array
    {
        return $this->http->delete($this->basePath("webhooks/{$webhookId}"));
    }
}
