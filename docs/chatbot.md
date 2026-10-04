# PCU Assistant

The chatbot combines deterministic PCU election-help answers with optional AI. Local answers are checked first; AI is used only when no local answer matches. AI replies are advisory and cannot read or change accounts, ballots, votes, eligibility, or election results. Published election information and election decisions remain controlled by the application.

## Multi-provider routing

The default `router` provider tries configured AI services in server-side priority order. Common questions such as how to vote or register are handled locally and do not consume provider requests. The default order is Groq followed by Gemini:

```dotenv
CHATBOT_AI_PROVIDER=router
CHATBOT_AI_PRIMARY_PROVIDER=groq
CHATBOT_AI_FALLBACK_PROVIDERS=gemini
CHATBOT_AI_MAX_PROVIDERS_PER_REQUEST=2
CHATBOT_AI_MAX_ATTEMPTS_PER_REQUEST=4
CHATBOT_AI_MAX_REQUESTS_PER_MINUTE=10
CHATBOT_AI_MAX_TOTAL_SECONDS=35
CHATBOT_AI_MAX_OUTPUT_TOKENS=500
```

The router skips providers without a configured key, so leaving Groq unconfigured uses Gemini when its key is present. It makes one request per provider, cools down providers after rate-limit/quota/service errors, and falls back to deterministic help if no provider responds. The total attempt cap includes individual Gemini model attempts; the request/time caps prevent provider failover from multiplying outbound calls without bound. Authentication/configuration errors are not repeatedly retried against the same provider. Model choice and provider selection are server-side; the browser cannot send a trusted model or endpoint.

Use a shared Laravel cache store across all application workers so the outbound budget, cooldowns, and locks apply across the deployment. This project defaults to the database cache store. The application-level cap is a soft safeguard, not a promise of provider capacity or a replacement for account-specific limits.

## Groq

Groq uses its OpenAI-compatible Chat Completions API. Current documented endpoint:

`https://api.groq.com/openai/v1/chat/completions`

Configure a Groq key only in the local `.env`:

```dotenv
CHATBOT_GROQ_API_KEY=your-groq-api-key
CHATBOT_GROQ_ENDPOINT=https://api.groq.com/openai/v1/chat/completions
CHATBOT_GROQ_MODEL=openai/gpt-oss-20b
CHATBOT_GROQ_FALLBACK_MODELS=openai/gpt-oss-120b,qwen/qwen3.8-27b
CHATBOT_GROQ_TIMEOUT=20
```

Create and manage keys in the [Groq Console](https://console.groq.com/keys). Keep the key server-side; never add it to source, Blade, JavaScript, browser requests, logs, or documentation. The default model is configurable and should be checked against the active models available to your Groq organization. Groq documents `GET https://api.groq.com/openai/v1/models` for retrieving hosted active models; the application does not call this endpoint on student requests.

The server tries the configured Groq models sequentially: `CHATBOT_GROQ_MODEL` first, then each comma-separated model in `CHATBOT_GROQ_FALLBACK_MODELS`. The defaults are `openai/gpt-oss-20b`, `openai/gpt-oss-120b`, and `qwen/qwen3.8-27b`. The browser cannot select or override a provider or model. Only retryable model failures advance to the next Groq model; invalid credentials and forbidden responses stop Groq model rotation and may use the configured Gemini provider instead. Invalid requests do not cause retries against other Groq models. Per-model cooldowns prevent repeatedly calling a temporarily unhealthy model.

The shared `CHATBOT_AI_MAX_ATTEMPTS_PER_REQUEST=4` cap covers the entire request: at most three Groq model calls and one Gemini model call with the default list. If the configured cap or time limit is reached sooner, routing stops. Failover is for service resilience, not quota circumvention; each model/account may have different limits, and the application does not promise or multiply provider capacity.

The provider sends a capped text-only `system`, `user`, and `assistant` message list to Chat Completions. It uses the centralized PCU system instruction and existing input/history sanitization, limits output, and uses the documented conservative `reasoning_effort=low` option shared by the configured models. It does not enable tools, function calls, streaming, image/audio, SQL, account, ballot, or vote access. Only the final message content is returned; provider reasoning is not surfaced. Groq-specific request/response structures remain inside `GroqProvider`.

Groq's current documentation describes organization-level limits across RPM, RPD, TPM, TPD, and, for some organizations, ITPM/OTPM. Exact limits vary by organization, model, and account and are shown in the [Groq Console limits page](https://console.groq.com/settings/limits); this project does not assume a guaranteed free-tier quota. On HTTP 429 the provider reads `retry-after` and rate-limit reset headers when present, distinguishes likely daily quota messages from short-term rate limits, and cools down the affected model. Remaining model attempts and then Gemini are used only within the shared request cap. Transient errors also fail over without repeated retries of the same model. No key rotation or quota-circumvention behavior is used.

For troubleshooting, confirm the configured model IDs are active for the Groq organization and that the model is enabled for the key. Groq's current model pages list [`openai/gpt-oss-20b`](https://console.groq.com/docs/model/openai/gpt-oss-20b), [`openai/gpt-oss-120b`](https://console.groq.com/docs/model/openai/gpt-oss-120b), and [`qwen/qwen3.8-27b`](https://console.groq.com/docs/model/qwen/qwen3.8-27b). The application does not make model-list or health-check requests per student message.

Groq's [data documentation](https://console.groq.com/docs/your-data) says inference inputs and outputs are not retained by default, but may be temporarily logged for service reliability or abuse monitoring (up to 30 days, unless applicable requirements call for longer). Organization data-control settings, including Zero Data Retention, are managed in the Groq Console. Do not submit passwords, verification codes, ballot selections, or sensitive student information.

## Gemini

Gemini remains the configured fallback and can still be selected directly:

```dotenv
CHATBOT_AI_PROVIDER=gemini
CHATBOT_AI_API_KEY=your-gemini-api-key
CHATBOT_AI_MODEL=gemini-3.8-flash
CHATBOT_AI_FALLBACK_MODELS=gemini-3.5-flash-lite,gemini-2.5-flash-lite
CHATBOT_AI_TIMEOUT=20
```

Create a key in [Google AI Studio](https://aistudio.google.com/apikey). The Gemini adapter uses Google's stateless Interactions REST API at `https://generativelanguage.googleapis.com/v1beta/interactions`, authenticated with `x-goog-api-key`, and `store: false`. Local election facts and the same safety instructions are supplied to each configured Gemini model. Google's free-tier terms may allow submitted content to be used to improve its products. Quotas and model access vary by project; check [AI Studio rate limits](https://aistudio.google.com/rate-limit).

Within Gemini, configured models are tried in order up to `CHATBOT_AI_MAX_MODELS_PER_REQUEST`. Daily-quota errors cool a model down until the next midnight Pacific reset; short-term rate limits and temporary service errors receive shorter cooldowns. Model rotation does not promise extra capacity or bypass project limits.

## Other provider modes

- `CHATBOT_AI_PROVIDER=router`: Groq first, then configured fallback providers (default `gemini`).
- `CHATBOT_AI_PROVIDER=groq`: use Groq directly.
- `CHATBOT_AI_PROVIDER=gemini`: use Gemini and its configured model fallback list.
- `CHATBOT_AI_PROVIDER=openai` or `openai-compatible`: use the existing OpenAI-compatible adapter.
- `CHATBOT_AI_PROVIDER=mock`: local mock response; not a production AI service.

Local deterministic answers continue to run if AI is disabled or unavailable. To disable network AI, remove/clear the configured provider API keys and run `php artisan config:clear`. To switch between Groq and Gemini as router primary, set `CHATBOT_AI_PRIMARY_PROVIDER`; list fallbacks in priority order in `CHATBOT_AI_FALLBACK_PROVIDERS`. Only use `groq` and/or `gemini` in the router fallback list.

## Safety, privacy, and logging

All providers receive the same centralized PCU instructions, approved election facts, capped conversation history, and sanitized messages. Legacy `bot` history is normalized to `assistant`. Outputs are treated as text and HTML is stripped before display. The providers have no tool, SQL, election database, account, ballot, or voting access. The application remains authoritative for election dates, status, eligibility, candidates, rules, and results.

Provider failure logs contain only provider/model identifiers, normalized error category, HTTP status, elapsed time/attempt order, and fallback metadata; they do not include raw provider responses, prompts, credentials, or student messages. Chat transcripts are not stored by PCU by default. Enable `CHATBOT_LOG_CONVERSATIONS=true` only if institutional retention/access policies approve it; records older than `CHATBOT_LOG_RETENTION_DAYS` are removed during chatbot requests. PCU transcript settings do not control provider-side processing or retention.

The chatbot endpoint retains its existing CSRF protection and student request throttle. `CHATBOT_AI_MAX_REQUESTS_PER_MINUTE` limits outgoing AI calls across the application; `CHATBOT_AI_MAX_TOTAL_SECONDS` bounds routing time; `CHATBOT_AI_MAX_ATTEMPTS_PER_REQUEST` bounds total provider/model calls. Google and Groq enforce their own project/organization limits, which may be lower.

After changing `.env`, run `php artisan config:clear` and restart the local server as needed. Automated chatbot tests fake external requests and do not consume Groq or Gemini quota.

## Official provider documentation

- Groq [OpenAI compatibility](https://console.groq.com/docs/openai)
- Groq [Chat Completions](https://console.groq.com/docs/text-chat)
- Groq [models](https://console.groq.com/docs/models)
- Groq [rate limits](https://console.groq.com/docs/rate-limits)
- Groq [errors](https://console.groq.com/docs/errors)
- Groq [data handling](https://console.groq.com/docs/your-data)
- Gemini [Interactions API](https://ai.google.dev/gemini-api/docs/interactions-overview)
- Gemini [models](https://ai.google.dev/gemini-api/docs/models)
- Gemini [pricing](https://ai.google.dev/gemini-api/docs/pricing)
- Gemini [rate limits](https://ai.google.dev/gemini-api/docs/rate-limits)
- Gemini [API errors](https://ai.google.dev/gemini-api/docs/api-errors)
