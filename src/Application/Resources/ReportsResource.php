<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application\Resources;

/**
 * Reports Resource
 *
 * Endpoints (v1):
 *   GET /api/v1/accounts/{account_id}/reports/events        - Account reporting events
 *   GET /api/v1/accounts/{account_id}/reports               - Account reports
 *   GET /api/v1/accounts/{account_id}/reports/summary       - Account reports summary
 *
 * Endpoints (v2):
 *   GET /api/v2/accounts/{account_id}/reports/conversations              - Account conversation metrics
 *   GET /api/v2/accounts/{account_id}/reports/conversations/             - Agent conversation metrics
 *   GET /api/v2/accounts/{account_id}/reports/conversations/channel      - Stats by channel type (v4.10.0+)
 *   GET /api/v2/accounts/{account_id}/reports/conversations/inbox        - Stats by inbox
 *   GET /api/v2/accounts/{account_id}/reports/conversations/agent        - Stats by agent
 *   GET /api/v2/accounts/{account_id}/reports/conversations/team         - Stats by team
 *   GET /api/v2/accounts/{account_id}/reports/first_response             - First response time distribution
 *   GET /api/v2/accounts/{account_id}/reports/inbox_label                - Inbox-label matrix
 *   GET /api/v2/accounts/{account_id}/reports/outgoing_messages          - Outgoing messages grouped by entity
 */
class ReportsResource extends BaseResource
{
    // -------------------------------------------------------------------------
    // V1 Reports
    // -------------------------------------------------------------------------

    /**
     * Get account reporting events.
     *
     * @param array $params Filter parameters
     */
    public function events(array $params = []): array
    {
        return $this->http->get($this->basePath('reports/events'), $this->filterParams($params));
    }

    /**
     * Get account reports.
     *
     * @param array $params {
     *   @type string $metric      Metric type (account, agent, inbox, label, team)
     *   @type string $type        Report type (account, agent, inbox, label, team, contact)
     *   @type int    $id          Agent/inbox/label/team ID (required for non-account types)
     *   @type int    $since       Start timestamp (Unix)
     *   @type int    $until       End timestamp (Unix)
     *   @type string $group_by    Grouping (day, week, month)
     * }
     */
    public function get(array $params = []): array
    {
        return $this->http->get($this->basePath('reports'), $this->filterParams($params));
    }

    /**
     * Get account reports summary.
     *
     * @param array $params {
     *   @type string $type   Report type (account, agent, inbox, label, team)
     *   @type int    $id     Entity ID for non-account types
     *   @type int    $since  Start timestamp (Unix)
     *   @type int    $until  End timestamp (Unix)
     * }
     */
    public function summary(array $params = []): array
    {
        return $this->http->get($this->basePath('reports/summary'), $this->filterParams($params));
    }

    // -------------------------------------------------------------------------
    // V2 Reports
    // -------------------------------------------------------------------------

    /**
     * Get account-level conversation metrics.
     *
     * @param array $params Filter params (since, until, grouping)
     */
    public function accountConversationMetrics(array $params = []): array
    {
        return $this->http->get($this->basePathV2('reports/conversations'), $this->filterParams($params));
    }

    /**
     * Get agent-level conversation metrics (open/unattended counts per agent).
     *
     * @param array $params Filter params
     */
    public function agentConversationMetrics(array $params = []): array
    {
        return $this->http->get($this->basePathV2('reports/conversations/'), $this->filterParams($params));
    }

    /**
     * Get conversation statistics grouped by channel type.
     * Returns open, resolved, pending, snoozed, and total counts per channel.
     *
     * NOTE: Requires Chatwoot version 4.10.0 or above.
     *
     * @param array $params {
     *   @type int $since  Start timestamp
     *   @type int $until  End timestamp
     * }
     */
    public function conversationsByChannel(array $params = []): array
    {
        return $this->http->get(
            $this->basePathV2('reports/conversations/channel'),
            $this->filterParams($params)
        );
    }

    /**
     * Get conversation statistics grouped by inbox.
     *
     * @param array $params Filter params (since, until)
     */
    public function conversationsByInbox(array $params = []): array
    {
        return $this->http->get(
            $this->basePathV2('reports/conversations/inbox'),
            $this->filterParams($params)
        );
    }

    /**
     * Get conversation statistics grouped by agent.
     *
     * @param array $params Filter params (since, until)
     */
    public function conversationsByAgent(array $params = []): array
    {
        return $this->http->get(
            $this->basePathV2('reports/conversations/agent'),
            $this->filterParams($params)
        );
    }

    /**
     * Get conversation statistics grouped by team.
     *
     * @param array $params Filter params (since, until)
     */
    public function conversationsByTeam(array $params = []): array
    {
        return $this->http->get(
            $this->basePathV2('reports/conversations/team'),
            $this->filterParams($params)
        );
    }

    /**
     * Get first response time distribution by channel.
     *
     * @param array $params Filter params (since, until)
     */
    public function firstResponseTimeDistribution(array $params = []): array
    {
        return $this->http->get(
            $this->basePathV2('reports/first_response'),
            $this->filterParams($params)
        );
    }

    /**
     * Get inbox-label matrix report.
     *
     * @param array $params Filter params (since, until)
     */
    public function inboxLabelMatrix(array $params = []): array
    {
        return $this->http->get(
            $this->basePathV2('reports/inbox_label'),
            $this->filterParams($params)
        );
    }

    /**
     * Get outgoing message counts grouped by entity.
     *
     * @param array $params Filter params (since, until, group_by)
     */
    public function outgoingMessageCounts(array $params = []): array
    {
        return $this->http->get(
            $this->basePathV2('reports/outgoing_messages'),
            $this->filterParams($params)
        );
    }
}
