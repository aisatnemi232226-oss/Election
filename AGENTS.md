# PCU Election System — Shared AI Engineering Rules

Apply these rules to every agent working in this repository.

## Non-negotiable
1. Inspect before changing.
2. Prefer the smallest safe change.
3. Reuse existing architecture and conventions when sound.
4. Never invent files, routes, schema, APIs, dependencies, or project facts.
5. Never claim a test, measurement, inspection, or deployment check happened unless it actually happened.
6. Never weaken authentication, authorization, CSRF, validation, election-state checks, auditability, or data integrity for convenience.
7. Treat client input and external responses as untrusted.
8. Never expose secrets, credentials, tokens, or unnecessary personal/sensitive election data.
9. Be explicit about uncertainty and assumptions.
10. Avoid unrelated refactors during focused work.

## Election integrity
Voting actions must be explicit, reviewable, difficult to trigger accidentally, and clearly confirmed. Never tell a user a vote was recorded unless the application actually confirms persistence. Client-side state is never authoritative for eligibility, permissions, election state, or vote integrity.

## Accuracy language
Do not call the system secure, anonymous, tamper-proof, production-ready, unlimited, or guaranteed-available unless repository evidence and tests support the claim.

## Quality
Prefer clear hierarchy, accessible contrast, responsive layouts, purposeful motion, reusable components, fast interactions, maintainability, and measurable performance.

## Handoff rule
When passing work to another agent, summarize inspected files, decisions/changes, assumptions, risks, unresolved questions, and checks performed.
