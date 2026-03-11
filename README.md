# Chatwoot PHP SDK

A full-featured PHP SDK for the [Chatwoot](https://www.chatwoot.com) API with a fluent interface, covering all three API families: **Application**, **Client**, and **Platform**.

**Author:** Ramiro Estrella  
**License:** MIT  
**Requires:** PHP 8.1+, Guzzle 7+  
**Tested against:** Chatwoot 4.11.x

---

## Installation

```bash
composer require ramiroestrella/chatwoot-php-sdk
```

---

## Quick Start

```php
use RamiroEstrella\ChatwootPhpSdk\ChatwootClient;

$chatwoot = new ChatwootClient(
    baseUrl:   'https://app.chatwoot.com',
    apiToken:  'your_api_access_token',
    accountId: 1
);
```

---

## Typed DTOs

All API responses are returned as **typed DTO objects** instead of plain arrays. Every property is nullable and type-safe — the SDK handles all coercion and response-unwrapping automatically, including Chatwoot 4.x's nested `{ "payload": { "contact": {...} } }` envelope format.

```php
$contact = $chatwoot->application()->contacts()->show(42);

echo $contact->name;    // string
echo $contact->email;   // string
echo $contact->blocked; // bool
echo $contact->id;      // int
```

### Available DTOs

| DTO | Used by |
|-----|---------|
| `ContactDTO` | contacts |
| `ConversationDTO` | conversations |
| `ConversationMetaDTO` | nested inside `ConversationDTO` |
| `MessageDTO` | messages |
| `AgentDTO` | agents, team members, inbox members, platform users |
| `InboxDTO` | inboxes |
| `TeamDTO` | teams |
| `WebhookDTO` | webhooks |
| `CannedResponseDTO` | canned responses |
| `AccountDTO` | account, platform accounts |
| `AgentBotDTO` | agent bots |
| `AutomationRuleDTO` | automation rules |
| `AuditLogDTO` | audit logs |
| `CustomAttributeDTO` | custom attributes |
| `ContactInboxDTO` | contact inbox links |

All DTOs extend `BaseDTO` which provides:

```php
$dto = ContactDTO::fromArray($responseArray); // hydrate from API response
$dtos = ContactDTO::collect($arrayOfArrays);  // hydrate a collection → ContactDTO[]
$dto->toArray();
$dto->toArray(excludeNull: true);
```

### Paginated Collections

List endpoints return a typed collection object:

```php
$contacts = $chatwoot->application()->contacts()->list(['page' => 1]);

$contacts->items;       // ContactDTO[]
$contacts->count;       // total records across all pages
$contacts->currentPage; // current page number
$contacts->isEmpty();
$contacts->first();     // ContactDTO|null
$contacts->map(fn($c) => $c->email);
$contacts->filter(fn($c) => $c->blocked === false);
```

| Collection | Used by |
|-----------|---------|
| `ContactCollection` | contacts list, search, filter |
| `ConversationCollection` | conversations list, filter, contact conversations |
| `MessageCollection` | messages list |

---

## Enums

Properties with a known set of values use PHP backed enums for full IDE autocomplete and type safety.

| Enum | Values |
|------|--------|
| `AgentRole` | `Agent`, `Administrator` |
| `AgentAvailabilityStatus` | `Available`, `Busy`, `Offline` |
| `ConversationStatus` | `Open`, `Resolved`, `Pending`, `Snoozed` |
| `ConversationPriority` | `Low`, `Medium`, `High`, `Critical` |
| `MessageType` *(int)* | `Incoming=0`, `Outgoing=1`, `Activity=2`, `Template=3` |
| `MessageStatus` | `Sent`, `Delivered`, `Read`, `Failed` |
| `MessageContentType` | `Text`, `InputEmail`, `Cards`, `InputSelect`, `Form`, `Article` |
| `CustomAttributeModel` *(int)* | `Conversation=0`, `Contact=1` |

```php
use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationStatus;
use RamiroEstrella\ChatwootPhpSdk\Enums\ConversationPriority;

$conv = $chatwoot->application()->conversations()->show($id);

$conv->status;                               // ConversationStatus enum
$conv->status->value;                        // 'open'
$conv->status === ConversationStatus::Open;  // true
$conv->priority?->value;                     // 'high'

// Methods accept both enums and plain strings
$chatwoot->application()->conversations()->toggleStatus($id, ConversationStatus::Resolved);
$chatwoot->application()->conversations()->toggleStatus($id, 'resolved'); // also works
```

---

## API Families

| Method | API | Auth | Availability |
|--------|-----|------|--------------|
| `$chatwoot->application()` | Application API | `api_access_token` | Cloud + Self-hosted |
| `$chatwoot->client($inboxIdentifier)` | Client API | inbox identifier string | Cloud + Self-hosted |
| `$chatwoot->platform($platformToken)` | Platform API | Platform App token | **Self-hosted only** |

---

## Application API

Used for agent/admin operations. Get your token from **Profile Settings → Access Token**.

### Account

```php
$account = $chatwoot->application()->account()->show();
$account = $chatwoot->application()->account()->update(['name' => 'My Support Team']);
```

### Profile

```php
$profile = $chatwoot->application()->profile()->get();
$chatwoot->application()->profile()->update(['display_name' => 'Agent Smith', 'availability' => 'busy']);
```

### Contacts

```php
// List → ContactCollection
$contacts = $chatwoot->application()->contacts()->list(['page' => 1]);

// CRUD → ContactDTO
$contact = $chatwoot->application()->contacts()->create(['name' => 'Bob', 'email' => 'bob@example.com']);
$contact = $chatwoot->application()->contacts()->show($contactId);
$contact = $chatwoot->application()->contacts()->update($contactId, ['name' => 'Robert']);
$chatwoot->application()->contacts()->delete($contactId);

// Search / filter → ContactCollection
$results  = $chatwoot->application()->contacts()->search('bob@example.com');
$filtered = $chatwoot->application()->contacts()->filter(['payload' => [...]]);

// Conversations → ConversationCollection
$convos = $chatwoot->application()->contacts()->conversations($contactId);

// Link to inbox → ContactInboxDTO  (source_id is the contact identifier for Client API)
$contactInbox = $chatwoot->application()->contacts()->createContactInbox($contactId, $inboxId);
echo $contactInbox->source_id;

// Contactable inboxes → ContactInboxDTO[]
$inboxes = $chatwoot->application()->contacts()->contactableInboxes($contactId);

// Merge → ContactDTO
$merged = $chatwoot->application()->contacts()->merge($parentId, $childId);
```

### Conversations

```php
// List → ConversationCollection
$conversations = $chatwoot->application()->conversations()->list([
    'status'   => 'open',
    'inbox_id' => 3,
    'page'     => 1,
]);

// Create → ConversationDTO
$conversation = $chatwoot->application()->conversations()->create([
    'source_id' => $contactInbox->source_id,
    'inbox_id'  => 3,
]);

// Show → ConversationDTO
$conv = $chatwoot->application()->conversations()->show($conversationId);
echo $conv->status->value;          // 'open' or 'pending' (depends on inbox config)
foreach ($conv->messages as $msg) { echo $msg->content; }

// Update → ConversationDTO
$conv = $chatwoot->application()->conversations()->update($conversationId, [
    'assignee_id' => $agentId,
]);

// Toggle status → ConversationDTO (re-fetches after toggle)
$conv = $chatwoot->application()->conversations()->toggleStatus($conversationId, ConversationStatus::Resolved);
$conv = $chatwoot->application()->conversations()->toggleStatus($conversationId, 'snoozed', time() + 3600);

// Toggle priority → ConversationDTO (re-fetches after toggle)
$conv = $chatwoot->application()->conversations()->togglePriority($conversationId, ConversationPriority::High);
$conv = $chatwoot->application()->conversations()->togglePriority($conversationId, null); // unset

// Labels
$chatwoot->application()->conversations()->addLabels($conversationId, ['vip', 'billing']); // string[]
$chatwoot->application()->conversations()->listLabels($conversationId);                    // string[]

// Custom attributes → ConversationDTO
$chatwoot->application()->conversations()->updateCustomAttributes($conversationId, ['order_id' => 'ORD-1']);

// Other
$chatwoot->application()->conversations()->toggleTypingStatus($conversationId, 'on');
$chatwoot->application()->conversations()->counts();
$chatwoot->application()->conversations()->filter($payload, page: 1); // ConversationCollection
```

> **Note:** New conversations may start as `pending` depending on your inbox configuration in Chatwoot 4.x.

> **Note:** `toggleStatus()` and `togglePriority()` both re-fetch the full conversation after toggling, because Chatwoot 4.x returns only `{ success: true, current_status: "..." }` from those endpoints — not a full conversation object.

### Messages

```php
// List → MessageCollection
$messages = $chatwoot->application()->messages()->list($conversationId);
foreach ($messages->items as $msg) {
    echo $msg->content . ' (type: ' . $msg->message_type->value . ')';
}

// Send text → MessageDTO
$msg = $chatwoot->application()->messages()->sendText($conversationId, 'Hello there!');

// Send private note → MessageDTO
$msg = $chatwoot->application()->messages()->sendPrivateNote($conversationId, 'Internal note');

// Send WhatsApp template → MessageDTO
$msg = $chatwoot->application()->messages()->sendWhatsAppTemplate(
    $conversationId,
    'Your order {{1}} is confirmed.',
    [
        'name'             => 'order_confirmation',
        'category'         => 'MARKETING',
        'language'         => 'en',
        'processed_params' => ['body' => ['1' => '12345']],
    ]
);

// Full create → MessageDTO
$msg = $chatwoot->application()->messages()->create($conversationId, [
    'content'      => 'Hello!',
    'message_type' => 'outgoing',
    'private'      => false,
]);

$chatwoot->application()->messages()->delete($conversationId, $messageId);
```

### Agents

```php
$agents = $chatwoot->application()->agents()->list();                                                        // AgentDTO[]
$agent  = $chatwoot->application()->agents()->create(['name' => 'Alice', 'email' => 'alice@example.com', 'role' => 'agent']);
$agent  = $chatwoot->application()->agents()->update($agentId, ['role' => 'administrator']);
$chatwoot->application()->agents()->delete($agentId);
```

### Inboxes

```php
$inboxes = $chatwoot->application()->inboxes()->list();                                                      // InboxDTO[]
$inbox   = $chatwoot->application()->inboxes()->show($inboxId);
$inbox   = $chatwoot->application()->inboxes()->create(['name' => 'Support', 'channel_type' => 'Channel::Api']);
$inbox   = $chatwoot->application()->inboxes()->update($inboxId, ['name' => 'New Name']);

// Agents → AgentDTO[]
$agents = $chatwoot->application()->inboxes()->listAgents($inboxId);
$agents = $chatwoot->application()->inboxes()->addAgents($inboxId, [$agentId1, $agentId2]);
$agents = $chatwoot->application()->inboxes()->updateAgents($inboxId, [$agentId1]);
$chatwoot->application()->inboxes()->removeAgent($inboxId, $agentId);

// Agent bot
$bot = $chatwoot->application()->inboxes()->showAgentBot($inboxId);  // AgentBotDTO|null
$chatwoot->application()->inboxes()->setAgentBot($inboxId, $botId);
$chatwoot->application()->inboxes()->setAgentBot($inboxId, null);    // remove
```

### Teams

```php
$teams  = $chatwoot->application()->teams()->list();
$team   = $chatwoot->application()->teams()->create('Support Team', 'Handles all support');
$team   = $chatwoot->application()->teams()->show($teamId);
$team   = $chatwoot->application()->teams()->update($teamId, ['name' => 'New Name']);
$chatwoot->application()->teams()->delete($teamId);

// Members → AgentDTO[]
$agents = $chatwoot->application()->teams()->listAgents($teamId);
$agents = $chatwoot->application()->teams()->addAgents($teamId, [$agentId]);
$agents = $chatwoot->application()->teams()->updateAgents($teamId, [$agentId]);
$chatwoot->application()->teams()->removeAgents($teamId, [$agentId]);
```

### Webhooks

```php
use RamiroEstrella\ChatwootPhpSdk\Application\Resources\WebhooksResource;

$webhooks = $chatwoot->application()->webhooks()->list();             // WebhookDTO[]

$webhook = $chatwoot->application()->webhooks()->create(
    'https://your-server.com/hook',
    [WebhooksResource::EVENT_CONVERSATION_CREATED, WebhooksResource::EVENT_MESSAGE_CREATED],
    'My Webhook'  // optional name
);

$webhook = $chatwoot->application()->webhooks()->update($webhookId, 'https://new-url.com', [
    WebhooksResource::EVENT_CONTACT_CREATED,
]);

$chatwoot->application()->webhooks()->delete($webhookId);
```

Available event constants: `EVENT_CONVERSATION_CREATED`, `EVENT_CONVERSATION_STATUS_CHANGED`, `EVENT_CONVERSATION_UPDATED`, `EVENT_CONTACT_CREATED`, `EVENT_CONTACT_UPDATED`, `EVENT_MESSAGE_CREATED`, `EVENT_MESSAGE_UPDATED`, `EVENT_WEBWIDGET_TRIGGERED`.

### Canned Responses

```php
$responses = $chatwoot->application()->cannedResponses()->list('greeting');           // CannedResponseDTO[]
$cr        = $chatwoot->application()->cannedResponses()->create('hi', 'Hello! How can I help?');
$cr        = $chatwoot->application()->cannedResponses()->update($id, ['content' => 'Updated text']);
$chatwoot->application()->cannedResponses()->delete($id);
```

### Custom Attributes

```php
// 0 = conversation attributes, 1 = contact attributes
$attrs = $chatwoot->application()->customAttributes()->list(0);      // CustomAttributeDTO[]
$attr  = $chatwoot->application()->customAttributes()->create([...]);
$attr  = $chatwoot->application()->customAttributes()->show($id);
$attr  = $chatwoot->application()->customAttributes()->update($id, [...]);
$chatwoot->application()->customAttributes()->delete($id);
```

### Automation Rules

```php
$rules = $chatwoot->application()->automationRules()->list();        // AutomationRuleDTO[]
$rule  = $chatwoot->application()->automationRules()->create([...]);
$rule  = $chatwoot->application()->automationRules()->show($id);
$rule  = $chatwoot->application()->automationRules()->update($id, [...]);
$chatwoot->application()->automationRules()->delete($id);
```

### Audit Logs

```php
$logs = $chatwoot->application()->auditLogs()->list(page: 1);       // AuditLogDTO[]
foreach ($logs as $log) {
    echo $log->action . ' by ' . $log->username;
}
```

### Conversation Assignments

```php
$chatwoot->application()->conversationAssignments()->getAssignee($conversationId);
$chatwoot->application()->conversationAssignments()->assignAgent($conversationId, $agentId);
$chatwoot->application()->conversationAssignments()->unassignAgent($conversationId);

$chatwoot->application()->conversationAssignments()->getParticipants($conversationId);
$chatwoot->application()->conversationAssignments()->addParticipants($conversationId, [$agentId]);
$chatwoot->application()->conversationAssignments()->updateParticipants($conversationId, [$agentId]);
$chatwoot->application()->conversationAssignments()->removeParticipants($conversationId, [$agentId]);
```

### Contact Labels

```php
$labels = $chatwoot->application()->contactLabels()->list($contactId);
$chatwoot->application()->contactLabels()->update($contactId, ['vip', 'enterprise']);
```

### Account Agent Bots

```php
$bots = $chatwoot->application()->agentBots()->list();              // AgentBotDTO[]
$bot  = $chatwoot->application()->agentBots()->create(['name' => 'My Bot', 'outgoing_url' => 'https://...']);
$bot  = $chatwoot->application()->agentBots()->show($id);
$bot  = $chatwoot->application()->agentBots()->update($id, ['name' => 'Updated']);
$chatwoot->application()->agentBots()->delete($id);
```

### Help Center

```php
$portals  = $chatwoot->application()->helpCenter()->listPortals();
$portal   = $chatwoot->application()->helpCenter()->createPortal(['slug' => 'help', 'name' => 'Help Center']);
$portal   = $chatwoot->application()->helpCenter()->showPortal('help');
$portal   = $chatwoot->application()->helpCenter()->updatePortal('help', ['name' => 'New Name']);

$articles = $chatwoot->application()->helpCenter()->listArticles('help', ['locale' => 'en', 'page' => 1]);
$article  = $chatwoot->application()->helpCenter()->createArticle('help', [
    'title'   => 'Getting Started',
    'content' => '<p>Welcome!</p>',
    'locale'  => 'en',
    'status'  => 'published',
]);
$article  = $chatwoot->application()->helpCenter()->showArticle('help', $articleId);
$article  = $chatwoot->application()->helpCenter()->updateArticle('help', $articleId, ['title' => 'New Title']);
$chatwoot->application()->helpCenter()->deleteArticle('help', $articleId);
```

### Custom Filters

```php
$filters = $chatwoot->application()->customFilters()->list('conversation');
$filter  = $chatwoot->application()->customFilters()->create([
    'name'        => 'Open VIP',
    'filter_type' => 'conversation',
    'query'       => ['payload' => [...]],
]);
$filter  = $chatwoot->application()->customFilters()->show($id);
$filter  = $chatwoot->application()->customFilters()->update($id, ['name' => 'Updated']);
$chatwoot->application()->customFilters()->delete($id);
```

### Integrations

```php
$apps  = $chatwoot->application()->integrations()->list();
$hook  = $chatwoot->application()->integrations()->createHook(['app_id' => 'slack', 'url' => 'https://...']);
$hook  = $chatwoot->application()->integrations()->updateHook($hookId, ['url' => 'https://new-url.com']);
$chatwoot->application()->integrations()->deleteHook($hookId);
```

### Reports

```php
// V1
$chatwoot->application()->reports()->get(['metric' => 'account', 'type' => 'account', 'since' => $ts, 'until' => $ts]);
$chatwoot->application()->reports()->summary(['type' => 'account', 'since' => $ts, 'until' => $ts]);

// V2 (Chatwoot 4.x — requires since/until params)
$chatwoot->application()->reports()->accountConversationMetrics(['since' => $ts, 'until' => $ts]);
$chatwoot->application()->reports()->agentConversationMetrics();
$chatwoot->application()->reports()->conversationsByChannel(['since' => $ts, 'until' => $ts]);
$chatwoot->application()->reports()->conversationsByInbox();
$chatwoot->application()->reports()->conversationsByTeam();
$chatwoot->application()->reports()->firstResponseTimeDistribution();
$chatwoot->application()->reports()->outgoingMessageCounts();
```

---

## Client API

For building custom chat interfaces for end-users. No agent token needed.

> **Important:** The inbox must be a **Website** (web widget) type. Email, API, and phone channel inboxes will return 404 on these endpoints.

**Finding your inbox identifier:** Go to **Settings → Inboxes → (your web widget inbox) → Collaboration tab**. Copy the long hex string — this is different from the numeric inbox ID.

```php
// Step 1: get a source_id for the contact via Application API
$contactInbox = $chatwoot->application()->contacts()->createContactInbox($contactId, $inboxId);
$sourceId = $contactInbox->source_id;

// Step 2: use the Client API with the inbox identifier string
$client = $chatwoot->client('abc123def456...');

// Contacts
$contact = $client->contacts()->create(['name' => 'Alice', 'email' => 'alice@example.com']);
$client->contacts()->get($sourceId);
$client->contacts()->update($sourceId, ['name' => 'Alice Updated']);

// Conversations
$conversation = $client->conversations($sourceId)->create();
$client->conversations($sourceId)->list();
$client->conversations($sourceId)->get($conversationId);
$client->conversations($sourceId)->resolve($conversationId);
$client->conversations($sourceId)->toggleTyping($conversationId, 'on');
$client->conversations($sourceId)->updateLastSeen($conversationId);

// Messages
$client->messages($sourceId)->list($conversationId);
$client->messages($sourceId)->send($conversationId, 'Hello, I need help!');
```

---

## Platform API

For installation-level management. **Self-hosted only.**

Get a token at `https://your-domain.com/super_admin` → Platform Apps → New → copy Access Token.

```php
$platform = $chatwoot->platform('your_platform_app_token');

// Accounts → AccountDTO
$account = $platform->accounts()->create(['name' => 'ACME Corp']);
$account = $platform->accounts()->show($accountId);
$account = $platform->accounts()->update($accountId, ['name' => 'Updated']);
$platform->accounts()->delete($accountId);

// Users → AgentDTO
$user = $platform->users()->create([
    'name'                  => 'John Doe',
    'email'                 => 'john@example.com',
    'password'              => 'securePass123!',
    'password_confirmation' => 'securePass123!',
]);
$user     = $platform->users()->show($userId);
$user     = $platform->users()->update($userId, ['name' => 'John Smith']);
$loginUrl = $platform->users()->getLoginUrl($userId);  // returns SSO URL
$platform->users()->delete($userId);

// Account Users
$members = $platform->accountUsers()->list($accountId);
$member  = $platform->accountUsers()->create($accountId, $userId, 'agent');
$platform->accountUsers()->delete($accountId, $userId);

// Platform Agent Bots → AgentBotDTO
$bots = $platform->agentBots()->list();
$bot  = $platform->agentBots()->create(['name' => 'My Bot']);
$bot  = $platform->agentBots()->show($botId);
$bot  = $platform->agentBots()->update($botId, ['name' => 'Updated']);
$platform->agentBots()->delete($botId);
```

---

## Error Handling

```php
use RamiroEstrella\ChatwootPhpSdk\Exceptions\AuthenticationException;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\NotFoundException;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\ValidationException;
use RamiroEstrella\ChatwootPhpSdk\Exceptions\ApiException;

try {
    $contact = $chatwoot->application()->contacts()->show(999999);
} catch (AuthenticationException $e) {
    // 401 — invalid or missing API token
} catch (NotFoundException $e) {
    // 404 — resource not found
} catch (ValidationException $e) {
    // 422 — validation failed
    print_r($e->getErrors());
} catch (ApiException $e) {
    // any other API error
    echo "Error [{$e->getCode()}]: " . $e->getMessage();
}
```

---

## Advanced: Custom HTTP Client & Options

Pass Guzzle options directly via the `options` array:

```php
$chatwoot = new ChatwootClient(
    baseUrl:   'https://chatwoot.mycompany.com',
    apiToken:  'token',
    accountId: 1,
    options:   [
        'timeout' => 60,
        'verify'  => false,  // disable SSL verification
        'proxy'   => 'http://proxy.example.com:8080',
    ]
);
```

The SDK type-hints against `HttpClientInterface`, so you can inject your own HTTP implementation for logging, retries, or testing:

```php
use RamiroEstrella\ChatwootPhpSdk\Http\HttpClientInterface;

class LoggingHttpClient implements HttpClientInterface { ... }

$chatwoot = new ChatwootClient(baseUrl: '...', apiToken: '...', accountId: 1);
$chatwoot->setHttpClient(new LoggingHttpClient());
```

---

## Running Tests

### Unit Tests

Unit tests use PHPUnit mocks and make **no real HTTP calls**. They run entirely offline and are safe to run at any time.

```bash
# Install dependencies
composer install

# Run all unit tests
./vendor/bin/phpunit --testsuite Unit

# With descriptive test names
./vendor/bin/phpunit --testsuite Unit --testdox

# Run a specific resource's tests
./vendor/bin/phpunit --testsuite Unit --filter ContactsResourceTest --testdox
./vendor/bin/phpunit --testsuite Unit --filter ConversationsResourceTest --testdox
```

**Expected result:** 263 tests, 522 assertions, all passing.

### Integration Tests

Integration tests hit a **real Chatwoot instance**. They create real resources, assert against live API responses, and clean up after themselves using `finally` blocks — your instance will not be left with test data.

**Step 1 — Copy and fill in the env file:**

```bash
cp .env.integration.example .env.integration
```

Edit `.env.integration` with your values:

```bash
# Required for all integration tests
CHATWOOT_BASE_URL=https://chat.example.com
CHATWOOT_API_TOKEN=your_agent_api_access_token
CHATWOOT_ACCOUNT_ID=1

# Required for conversation and assignment tests
# Find: Settings → Inboxes → [your inbox] → edit → numeric ID in the URL
CHATWOOT_INBOX_ID=3

# Required for Client API tests — must be a Website (web widget) inbox
# Find: Settings → Inboxes → [web widget inbox] → Collaboration tab → Inbox Identifier
CHATWOOT_INBOX_IDENTIFIER=your_inbox_identifier_string

# Required for Platform API tests (self-hosted only)
# Create: https://your-domain.com/super_admin → Platform Apps → New
CHATWOOT_PLATFORM_TOKEN=your_platform_app_token
```

**Step 2 — Load env vars and run:**

```bash
set -a && source .env.integration && set +a

# Run all integration tests
./vendor/bin/phpunit --testsuite Integration --testdox

# Run a specific test class
./vendor/bin/phpunit --testsuite Integration --filter ApplicationApiTest --testdox
./vendor/bin/phpunit --testsuite Integration --filter ClientApiTest --testdox
./vendor/bin/phpunit --testsuite Integration --filter PlatformApiTest --testdox
```

**Run unit + integration together:**

```bash
set -a && source .env.integration && set +a
./vendor/bin/phpunit --testdox
```

#### What the integration tests cover

| Test class | Covers |
|-----------|--------|
| `ApplicationApiTest` | Account, profile, agents, contacts (CRUD + search + filter + merge), inboxes, conversations (status, priority, labels, messages, assignments), teams, webhooks, canned responses, custom attributes, audit logs, reports (v1 + v2), contact labels, automation rules |
| `ClientApiTest` | Contact lifecycle, conversation lifecycle (create/list/resolve/typing), messages — all via the public Client API |
| `PlatformApiTest` | Platform accounts (CRUD), platform users (CRUD + SSO URL), account user membership, platform agent bots |

#### Notes

- Tests that require `CHATWOOT_INBOX_ID`, `CHATWOOT_INBOX_IDENTIFIER`, or `CHATWOOT_PLATFORM_TOKEN` skip gracefully if the env var is not set.
- The Client API tests skip if the inbox identifier points to a non-widget inbox type.
- Platform agent bots skip if the endpoint returns 500 (known bug in some Chatwoot 4.x builds).

---

## Chatwoot 4.x Compatibility Notes

This SDK was built and tested against **Chatwoot 4.11.2** and handles several 4.x-specific behaviors transparently:

- **Nested response envelopes** — single-resource endpoints return `{ "payload": { "contact": {...} } }`. The SDK unwraps these automatically.
- **Webhook list shape** — the list endpoint returns `{ "payload": { "webhooks": [...] } }` (nested under a key), while create/update return `{ "payload": { "webhook": {...} } }`. Both are handled.
- **Toggle endpoints** — `toggle_status` and `toggle_priority` return only `{ success: true, current_status: "..." }`, not a full conversation. The SDK re-fetches the conversation automatically so the return value is always a complete `ConversationDTO`.
- **Pending conversations** — new conversations may start as `pending` instead of `open` depending on inbox configuration.
- **Team name casing** — Chatwoot lowercases team names server-side.
- **Reports v2** — the v2 report endpoints require `since`/`until` epoch timestamp parameters.

---

## Directory Structure

```
src/
├── ChatwootClient.php
├── Http/
│   ├── HttpClient.php
│   └── HttpClientInterface.php
├── Exceptions/
│   ├── ChatwootException.php
│   ├── ApiException.php
│   ├── AuthenticationException.php
│   ├── NotFoundException.php
│   └── ValidationException.php
├── Enums/
│   ├── AgentAvailabilityStatus.php
│   ├── AgentRole.php
│   ├── ConversationPriority.php
│   ├── ConversationStatus.php
│   ├── CustomAttributeModel.php
│   ├── MessageContentType.php
│   ├── MessageStatus.php
│   └── MessageType.php
├── DTO/
│   ├── BaseDTO.php
│   ├── AccountDTO.php         ├── AgentBotDTO.php
│   ├── AgentDTO.php           ├── AuditLogDTO.php
│   ├── AutomationRuleDTO.php  ├── CannedResponseDTO.php
│   ├── ContactDTO.php         ├── ContactInboxDTO.php
│   ├── ConversationDTO.php    ├── ConversationMetaDTO.php
│   ├── CustomAttributeDTO.php ├── InboxDTO.php
│   ├── MessageDTO.php         ├── TeamDTO.php
│   ├── WebhookDTO.php
│   └── Collections/
│       ├── PaginatedCollection.php
│       ├── ContactCollection.php
│       ├── ConversationCollection.php
│       └── MessageCollection.php
├── Application/
│   ├── ApplicationClient.php
│   └── Resources/
│       ├── BaseResource.php
│       ├── AccountResource.php        ├── AccountAgentBotsResource.php
│       ├── AgentsResource.php         ├── AuditLogsResource.php
│       ├── AutomationRulesResource.php ├── CannedResponsesResource.php
│       ├── ContactLabelsResource.php  ├── ContactsResource.php
│       ├── ConversationAssignmentsResource.php
│       ├── ConversationsResource.php  ├── CustomAttributesResource.php
│       ├── CustomFiltersResource.php  ├── HelpCenterResource.php
│       ├── InboxesResource.php        ├── IntegrationsResource.php
│       ├── MessagesResource.php       ├── ProfileResource.php
│       ├── ReportsResource.php        ├── TeamsResource.php
│       └── WebhooksResource.php
├── Client/
│   ├── ClientApiClient.php
│   └── Resources/
│       ├── BaseClientResource.php
│       ├── ContactsApiResource.php
│       ├── ConversationsApiResource.php
│       └── MessagesApiResource.php
└── Platform/
    ├── PlatformClient.php
    └── Resources/
        ├── BasePlatformResource.php
        ├── PlatformAccountsResource.php
        ├── PlatformAccountUsersResource.php
        ├── PlatformAgentBotsResource.php
        └── PlatformUsersResource.php

tests/
├── Unit/
│   ├── Application/    (unit tests for all 20 Application resources)
│   ├── Client/         (unit tests for Client API resources)
│   ├── Platform/       (unit tests for Platform API resources)
│   ├── DTO/            (BaseDTO hydration, enums, paginated collections)
│   ├── ChatwootClientTest.php
│   └── ExceptionsTest.php
└── Integration/
    ├── IntegrationTestCase.php
    ├── ApplicationApiTest.php
    ├── ClientApiTest.php
    └── PlatformApiTest.php
```

---

## License

MIT — see [LICENSE](LICENSE).
