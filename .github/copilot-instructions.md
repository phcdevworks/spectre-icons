# GitHub Copilot Instructions - Spectre Icons

`AGENTS.md` is the central AI coordination document for this repository. This
file defines Copilot support behavior only.

## First Read Order

Before making suggestions or edits, read:

1. `AGENTS.md`
2. `CLAUDE.md`
3. `CODEX.md`
4. `package.json`
5. this file

## Role

GitHub Copilot is the general development support assistant. Copilot supports
inline suggestions, small code changes, test hints, TypeScript help, API usage
hints, and focused refactor ideas.

Copilot is support-only and does not own architecture, implementation
leadership, release decisions, production stabilization ownership,
repository-wide governance, or automated maintenance ownership.

## Team Boundaries

- Bradley Potts: human owner and final release authority.
- Claude Code: lead implementation and architecture owner.
- OpenAI Codex: documentation, release-readiness, stabilization, repo hygiene,
  changelog/release-note, and configuration-standardization owner.
- GitHub Copilot: development support assistant.
- Google Jules: small automated maintenance and dependency micro-updates.

When instructions conflict, follow: Bradley -> `AGENTS.md` -> `CLAUDE.md` ->
`CODEX.md` -> this file.

## Repository Guardrails

- Preserve locked identifiers: `spectre-lucide`/`spectre-lucide-` and
  `spectre-fontawesome`/`spectre-fa-`.
- Do not edit bundled SVG source assets under `assets/iconpacks/`.
- Keep builder-agnostic logic in `includes/core/`.
- Keep Elementor-specific logic in `includes/elementor/`.
- Prefer manifest-driven registration over hardcoded mappings.
- Keep changes small, production-safe, and backward-compatible.

## Coding And Test Expectations

- Follow WordPress sanitization, escaping, nonce, and capability-check patterns.
- Reuse existing test helpers in `tests/e2e/support/`.
- Prefer deterministic Playwright waits (`expect`, `waitForURL`,
  `waitForResponse`) over timeout-based waits.
- Run the narrowest relevant checks, or `npm run check` when broad validation
  is needed.

## Documentation Expectations

When behavior, setup, compatibility, or validation flow changes, update related
docs (`README.md`, `readme.txt`, `CHANGELOG.md`, and `CONTRIBUTING.md`) and
defer release-readiness sign-off to Codex.
