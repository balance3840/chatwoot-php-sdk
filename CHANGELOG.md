# Changelog

All notable changes to this project will be documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.0.0] — 2026-03-12

### Added

**Core**
- `ChatwootClient` — main entry point, wires up all three API families
- `HttpClient` — Guzzle-backed HTTP client with automatic `api_access_token` header injection
- `HttpClientInterface` — abstraction over `HttpClient`, allowing custom HTTP implementations to be injected
- Exception hierarchy: `ChatwootException` → `ApiException` → `AuthenticationException` / `NotFoundException` / `ValidationException`

**Application API** (`/api/v1/accounts/{id}/...`)
- `AccountResource` — show, update
- `AgentsResource` — list, create, update, delete
- `ContactsResource` — list, create, show, update, delete, search, filter, merge, conversations, createContactInbox, contactableInboxes
- `ConversationsResource` — list, create, filter, show, update, toggleStatus, togglePriority, toggleTypingStatus, updateCustomAttributes, listLabels, addLabels, reportingEvents, counts
- `MessagesResource` — list, create, sendText, sendPrivateNote, sendWhatsAppTemplate, delete
- `InboxesResource` — list, show, create, update, showAgentBot, setAgentBot, listAgents, addAgents, updateAgents, removeAgent
- `TeamsResource` — list, create, show, update, delete, listAgents, addAgents, updateAgents, removeAgents
- `WebhooksResource` — list, create, update, delete
- `CannedResponsesResource` — list, create, update, delete
- `CustomAttributesResource` — list, create, show, update, delete
- `AuditLogsResource` — list
- `AutomationRulesResource` — list, create, show, update, delete
- `AccountAgentBotsResource` — list, create, show, update, delete
- `ConversationAssignmentsResource` — getAssignee, assignAgent, unassignAgent, getParticipants, addParticipants, updateParticipants, removeParticipants
- `ContactLabelsResource` — list, update
- `HelpCenterResource` — listPortals, createPortal, showPortal, updatePortal, listArticles, createArticle, showArticle, updateArticle, deleteArticle
- `ReportsResource` — events, get, summary (v1), accountConversationMetrics, agentConversationMetrics, conversationsByChannel, conversationsByInbox, conversationsByAgent, conversationsByTeam, firstResponseTimeDistribution, inboxLabelMatrix, outgoingMessageCounts (v2)
- `IntegrationsResource` — list, createHook, updateHook, deleteHook
- `CustomFiltersResource` — list, create, show, update, delete
- `ProfileResource` — get, update

**Client API** (`/public/api/v1/inboxes/{inbox_identifier}/...`)
- `ContactsApiResource` — create, get, update
- `ConversationsApiResource` — list, create, get, resolve, toggleTyping, updateLastSeen
- `MessagesApiResource` — list, create, send

**Platform API** (`/platform/api/v1/...`, self-hosted only)
- `PlatformAccountsResource` — create, show, update, delete
- `PlatformAccountUsersResource` — list, create, delete
- `PlatformUsersResource` — create, show, update, delete, getLoginUrl
- `PlatformAgentBotsResource` — list, create, show, update, delete

**DTO Layer**
- `BaseDTO` — reflection-based hydration, backed enum support, scalar type coercion, `collect()`, `toArray()`
- DTOs: `AccountDTO`, `AgentDTO`, `AgentBotDTO`, `AuditLogDTO`, `AutomationRuleDTO`, `CannedResponseDTO`, `ContactDTO`, `ContactInboxDTO`, `ConversationDTO`, `ConversationMetaDTO`, `CustomAttributeDTO`, `InboxDTO`, `MessageDTO`, `TeamDTO`, `WebhookDTO`
- Paginated collections: `ContactCollection`, `ConversationCollection`, `MessageCollection`

**Enums**
- `AgentRole` — `Agent`, `Administrator`
- `AgentAvailabilityStatus` — `Available`, `Busy`, `Offline`
- `ConversationStatus` — `Open`, `Resolved`, `Pending`, `Snoozed`
- `ConversationPriority` — `Low`, `Medium`, `High`, `Critical`
- `MessageType` *(int)* — `Incoming`, `Outgoing`, `Activity`, `Template`
- `MessageStatus` — `Sent`, `Delivered`, `Read`, `Failed`
- `MessageContentType` — `Text`, `InputEmail`, `Cards`, `InputSelect`, `Form`, `Article`
- `CustomAttributeModel` *(int)* — `Conversation`, `Contact`

**Tests**
- 253 unit tests, 500+ assertions
- Full mock-based coverage for every resource method across all three API families
- DTO hydration, enum coercion, type safety, and collection behaviour tested in isolation
