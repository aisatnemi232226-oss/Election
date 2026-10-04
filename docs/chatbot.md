# PCU Assistant

The existing chatbot combines local election-help answers with an optional AI provider. Local answers are tried first; AI is used only when no local answer matches. AI replies are advisory and cannot read or change accounts, ballots, votes, eligibility, or election results. Published election information and all election decisions remain controlled by the application.

## Gemini provider

Gemini is the default provider. Configure it only in the local `.env` file:

```dotenv
CHATBOT_AI_PROVIDER=gemini
CHATBOT_AI_API_KEY=your-gemini-api-key
CHATBOT_AI_MODEL=gemini-3.8-flash
CHATBOT_AI_FALLBACK_MODELS=gemini-3.5-flash-lite,gemini-2.5-flash-lite
CHATBOT_AI_TIMEOUT=20
CHATBOT_AI_MAX_OUTPUT_TOKENS=500
CHATBOT_AI_MAX_MODELS_PER_REQUEST=3
CHATBOT_AI_MAX_REQUESTS_PER_MINUTE=10
CHATBOT_AI_MAX_TOTAL_SECONDS=35
CHATBOT_LOG_CONVERSATIONS=false
CHATBOT_LOG_RETENTION_DAYS=30
```

Create a key at [Google AI Studio API keys](https://aistudio.google.com/apikey), then put it in the local `.env` as `CHATBOT_AI_API_KEY`. Do not paste it into chat, Blade, JavaScript, or `.env.example`, and do not commit it. Google says new AI Studio keys created since May 28, 2026 are authorization keys; unrestricted older standard keys are rejected, while standard keys with explicit restrictions remain supported. Check the key and project status in AI Studio if authentication fails.

The provider uses Google's stateless Interactions REST API at `https://generativelanguage.googleapis.com/v1beta/interactions`, authenticated with the `x-goog-api-key` header. It sends a capped, sanitized text-only dialogue, the same centralized PCU system instruction for every model, and `store: false`; no tools, Search grounding, image, audio, SQL, account, or ballot access are enabled. An optional `CHATBOT_AI_ENDPOINT` override is available for local testing.

The current documented model candidates are `gemini-3.8-flash` (primary), `gemini-3.5-flash-lite`, and `gemini-2.5-flash-lite` (fallbacks). Google's model pages document these text-capable models; model availability, free-tier eligibility, and quotas depend on the configured project and can change. Confirm the actual project's access and active per-model limits in [AI Studio rate limits](https://aistudio.google.com/rate-limit) before enabling a fallback. The router does not treat model rotation as unlimited capacity or as a way to bypass project limits. Google's free-tier terms may allow submitted content to be used to improve its products.

### Quota-aware model routing

Local election intents and approved help answers are checked first, so common questions do not use AI quota. For other questions, the server tries the configured primary model followed by the configured fallback list in order, up to `CHATBOT_AI_MAX_MODELS_PER_REQUEST`. The browser cannot choose a model.

Each model receives at most one API request per chatbot request. A short-lived shared Laravel cache health record and per-model cache lock track successful models and temporarily avoid models that are rate-limited, unavailable, or not found. A `429 rate_limit_exceeded`/`too_many_requests` cools down that model briefly; a documented daily `quota_exceeded` cools it down until the next midnight Pacific reset. A `503`, timeout, connection failure, or model-not-found error can move the request to the next configured model. Invalid requests, authentication/permission errors, and project prerequisite failures stop routing rather than blindly trying other models. If all candidates fail or are cooling down, the chatbot returns its local fallback/help options.

`CHATBOT_AI_MAX_REQUESTS_PER_MINUTE` is an application-wide soft cap on outgoing Gemini requests (10 per minute by default), in addition to the existing student-facing endpoint throttle. `CHATBOT_AI_MAX_TOTAL_SECONDS` bounds the time spent trying models (35 seconds by default). These application safeguards do not replace Google's per-project, per-model RPM/TPM/RPD or billing limits. Use a shared Laravel cache store across application workers so model cooldowns, locks, and the request budget are shared; this project defaults to the database cache store.

To disable network AI while keeping deterministic local answers, clear `CHATBOT_AI_API_KEY` and run `php artisan config:clear`. To change model order, edit `CHATBOT_AI_MODEL` and `CHATBOT_AI_FALLBACK_MODELS`; only include models confirmed for the configured API/project. If configuration is cached, run `php artisan config:clear` after editing `.env`.

If Laravel configuration is cached after changing `.env`, run `php artisan config:clear`. Never enter passwords, verification codes, ballot selections, or other sensitive student information in the chat. The provider removes common password/code patterns, email addresses, and long digit sequences before sending, but users should still avoid sharing sensitive data.

Provider failures are logged using only provider/model identifiers, attempt order, normalized error category, HTTP status, and whether fallback was used; raw provider responses, prompts, and credentials are not logged. Daily quota exhaustion is distinguished from short-term rate limits. Models are not repeatedly retried within one user request. When AI is unavailable, local answers remain available.

## Conversation privacy

Conversation transcripts are not stored by default. Enable `CHATBOT_LOG_CONVERSATIONS=true` only if the institution has approved the retention and access policy; likely password/code patterns are redacted before a log entry is written. When enabled, records older than `CHATBOT_LOG_RETENTION_DAYS` are removed during chatbot requests (30 days by default). This setting does not control provider-side retention.

## Provider development

`App\Services\Chatbot\AIProvider` defines the provider boundary. `gemini` is the default network adapter. `openai` and `openai-compatible` remain available for future switching; configure `CHATBOT_OPENAI_API_KEY`, an OpenAI model in `CHATBOT_AI_MODEL`, and (if needed) `CHATBOT_AI_ENDPOINT` for that provider. The `mock` provider can be selected locally to verify the UI without making an external request or using API credits; do not use mock mode as a production AI service.

To run the app locally, use the project's normal Laravel/Vite workflow (`composer run dev`). Add a Gemini key only to `.env`, then run `php artisan config:clear` and `php artisan serve` (or restart `composer run dev`). The automated chatbot tests fake every provider HTTP request and do not use API credits.

The manual end-to-end check was completed through the local student-facing chatbot after a Gemini key was configured: the route returned HTTP 200, `mode: ai`, and a visible Gemini response. This confirms the configured key/model worked for that request; future account quota and availability remain subject to Google's limits. Automated request/response tests fake provider HTTP calls and do not use live API credits.

## Official Gemini documentation

- [Interactions API overview](https://ai.google.dev/gemini-api/docs/interactions-overview)
- [Interactions API REST reference](https://ai.google.dev/api/interactions-api)
- [Gemini models](https://ai.google.dev/gemini-api/docs/models)
- [Gemini API keys](https://ai.google.dev/gemini-api/docs/api-key)
- [Pricing and free tier](https://ai.google.dev/gemini-api/docs/pricing)
- [Rate limits](https://ai.google.dev/gemini-api/docs/rate-limits)
- [API error handling](https://ai.google.dev/gemini-api/docs/api-errors)
