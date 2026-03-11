<?php

declare(strict_types=1);

namespace RamiroEstrella\ChatwootPhpSdk\Application;

use RamiroEstrella\ChatwootPhpSdk\Application\Resources\AccountAgentBotsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\AccountResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\AgentsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\AuditLogsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\AutomationRulesResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\CannedResponsesResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ContactLabelsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ContactsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ConversationAssignmentsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ConversationsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\CustomAttributesResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\CustomFiltersResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\HelpCenterResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\InboxesResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\IntegrationsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\MessagesResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ProfileResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\ReportsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\TeamsResource;
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\WebhooksResource;
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClient;

/**
 * Application API Client
 *
 * Entry point for all Application API resources.
 * Requires a user api_access_token.
 * Available on both Cloud and Self-hosted installations.
 *
 * Usage:
 *   $chatwoot->application()->account()->get()
 *   $chatwoot->application()->contacts()->list(['page' => 1])
 *   $chatwoot->application()->conversations()->create([...])
 *   $chatwoot->application()->messages()->sendText(123, 'Hello!')
 *   $chatwoot->application()->inboxes()->list()
 *   $chatwoot->application()->webhooks()->create('https://...', ['message_created'])
 *   $chatwoot->application()->reports()->summary(['type' => 'account'])
 *   $chatwoot->application()->teams()->list()
 *   $chatwoot->application()->agents()->list()
 *   $chatwoot->application()->profile()->get()
 */
class ApplicationClient
{
    private HttpClient $http;
    private int $accountId;

    // Lazily instantiated resource instances
    private ?AccountResource $accountResource                           = null;
    private ?AgentsResource $agentsResource                             = null;
    private ?ContactsResource $contactsResource                         = null;
    private ?ConversationsResource $conversationsResource               = null;
    private ?MessagesResource $messagesResource                         = null;
    private ?InboxesResource $inboxesResource                           = null;
    private ?IntegrationsResource $integrationsResource                 = null;
    private ?WebhooksResource $webhooksResource                         = null;
    private ?ReportsResource $reportsResource                           = null;
    private ?TeamsResource $teamsResource                               = null;
    private ?CannedResponsesResource $cannedResponsesResource           = null;
    private ?CustomAttributesResource $customAttributesResource         = null;
    private ?AuditLogsResource $auditLogsResource                       = null;
    private ?ContactLabelsResource $contactLabelsResource               = null;
    private ?AutomationRulesResource $automationRulesResource           = null;
    private ?HelpCenterResource $helpCenterResource                     = null;
    private ?ConversationAssignmentsResource $conversationAssignments   = null;
    private ?AccountAgentBotsResource $accountAgentBotsResource         = null;
    private ?CustomFiltersResource $customFiltersResource               = null;
    private ?ProfileResource $profileResource                           = null;

    public function __construct(HttpClient $http, int $accountId)
    {
        $this->http      = $http;
        $this->accountId = $accountId;
    }

    /**
     * Account management (get/update account details).
     */
    public function account(): AccountResource
    {
        return $this->accountResource ??= new AccountResource($this->http, $this->accountId);
    }

    /**
     * Agents management (list, create, update, delete agents).
     */
    public function agents(): AgentsResource
    {
        return $this->agentsResource ??= new AgentsResource($this->http, $this->accountId);
    }

    /**
     * Contacts management (CRUD, search, filter, merge, inboxes).
     */
    public function contacts(): ContactsResource
    {
        return $this->contactsResource ??= new ContactsResource($this->http, $this->accountId);
    }

    /**
     * Conversations management (list, create, update, status, labels, etc.).
     */
    public function conversations(): ConversationsResource
    {
        return $this->conversationsResource ??= new ConversationsResource($this->http, $this->accountId);
    }

    /**
     * Messages management (list, create, delete messages in a conversation).
     */
    public function messages(): MessagesResource
    {
        return $this->messagesResource ??= new MessagesResource($this->http, $this->accountId);
    }

    /**
     * Inboxes management (CRUD, agents, agent bots).
     */
    public function inboxes(): InboxesResource
    {
        return $this->inboxesResource ??= new InboxesResource($this->http, $this->accountId);
    }

    /**
     * Integrations management (list apps, create/update/delete hooks).
     */
    public function integrations(): IntegrationsResource
    {
        return $this->integrationsResource ??= new IntegrationsResource($this->http, $this->accountId);
    }

    /**
     * Webhooks management (list, create, update, delete webhooks).
     */
    public function webhooks(): WebhooksResource
    {
        return $this->webhooksResource ??= new WebhooksResource($this->http, $this->accountId);
    }

    /**
     * Reports (v1 + v2 metrics, summaries, channel/agent/team breakdowns).
     */
    public function reports(): ReportsResource
    {
        return $this->reportsResource ??= new ReportsResource($this->http, $this->accountId);
    }

    /**
     * Teams management (CRUD, manage team members).
     */
    public function teams(): TeamsResource
    {
        return $this->teamsResource ??= new TeamsResource($this->http, $this->accountId);
    }

    /**
     * Canned responses (predefined reply shortcuts).
     */
    public function cannedResponses(): CannedResponsesResource
    {
        return $this->cannedResponsesResource ??= new CannedResponsesResource($this->http, $this->accountId);
    }

    /**
     * Custom attributes definitions (contact and conversation attributes).
     */
    public function customAttributes(): CustomAttributesResource
    {
        return $this->customAttributesResource ??= new CustomAttributesResource($this->http, $this->accountId);
    }

    /**
     * Audit logs (admin activity trail).
     */
    public function auditLogs(): AuditLogsResource
    {
        return $this->auditLogsResource ??= new AuditLogsResource($this->http, $this->accountId);
    }

    /**
     * Contact labels (labels applied to contacts).
     */
    public function contactLabels(): ContactLabelsResource
    {
        return $this->contactLabelsResource ??= new ContactLabelsResource($this->http, $this->accountId);
    }

    /**
     * Automation rules (trigger-based automated workflows).
     */
    public function automationRules(): AutomationRulesResource
    {
        return $this->automationRulesResource ??= new AutomationRulesResource($this->http, $this->accountId);
    }

    /**
     * Help Center portals and articles.
     */
    public function helpCenter(): HelpCenterResource
    {
        return $this->helpCenterResource ??= new HelpCenterResource($this->http, $this->accountId);
    }

    /**
     * Conversation assignments (assign agents, manage participants).
     */
    public function conversationAssignments(): ConversationAssignmentsResource
    {
        return $this->conversationAssignments ??= new ConversationAssignmentsResource($this->http, $this->accountId);
    }

    /**
     * Account-level agent bots (CRUD for bot configurations).
     */
    public function agentBots(): AccountAgentBotsResource
    {
        return $this->accountAgentBotsResource ??= new AccountAgentBotsResource($this->http, $this->accountId);
    }

    /**
     * Custom filters (saved filter views for conversations and contacts).
     */
    public function customFilters(): CustomFiltersResource
    {
        return $this->customFiltersResource ??= new CustomFiltersResource($this->http, $this->accountId);
    }

    /**
     * Profile (currently authenticated user's profile).
     */
    public function profile(): ProfileResource
    {
        return $this->profileResource ??= new ProfileResource($this->http, $this->accountId);
    }
}
