# GitHub Copilot Instructions - Spectre Icons

`AGENTS.md` is the central AI coordination document for this repository. This
file provides Copilot-specific support guidance only.

## Role

GitHub Copilot is the general development support assistant for this repository.
Copilot improves developer productivity inside the IDE with inline suggestions,
small code edits, test hints, refactor ideas, TypeScript support, and API usage
hints.

Copilot is support-only and does not own:

- lead implementation decisions
- architecture direction
- release coordination
- production stabilization ownership
- repository-wide AI governance
- automated maintenance workflows
- git commits, pushes, tags, or release decisions

## Agent Boundaries

- Claude Code is the lead developer and primary implementation owner.
- OpenAI Codex owns documentation, releases, production stabilization, repo
  hygiene, changelog/release-note support, and config standardization.
- GitHub Copilot provides general development support.
- Google Jules handles automated maintenance for small fixes, dependency
  updates, and micro-updates.

When guidance appears to conflict, follow this order:

1. direct human instruction from Bradley Potts
2. `AGENTS.md`
3. `CLAUDE.md`
4. `CODEX.md`
5. this file

## Repository Coding Conventions

- Preserve serialization-anchored slugs and class prefixes:
  - spectre-lucide / spectre-lucide-
  - spectre-fontawesome / spectre-fa-
- Do not edit bundled SVG source assets under assets/iconpacks/.
- Keep builder-agnostic logic in includes/core/.
- Keep Elementor-specific logic in includes/elementor/.
- Prefer manifest-driven registration over hardcoded library mappings.
- Preserve backward compatibility for existing installs unless an explicit,
  documented migration is requested.

## PHP And WordPress Standards

- Follow WordPress sanitization, escaping, nonce, and capability-check patterns.
- Keep render paths defensive and preserve SVG sanitizer usage.
- Use small, targeted patches instead of broad rewrites.

## TypeScript And E2E Standards

- Prefer explicit typing; avoid introducing unnecessary any.
- Reuse helpers in tests/e2e/support/ before adding duplicate logic.
- Prefer deterministic waits (expect, waitForURL, waitForResponse) instead of
  waitForTimeout.
- Keep Playwright changes focused on user-visible icon flows.

## Validation Expectations

Pick the narrowest checks that cover the change:

- PHP lint and standards: composer lint or bin/lint-php.sh
- PHP behavior and registry/sanitizer/settings logic: composer test
- Elementor integration changes: npm run test:e2e:elementor
- Activation and settings behavior: npm run test:e2e:smoke

CI currently validates:

- composer validate --no-check-lock
- composer test
- composer lint

## Documentation Expectations

When behavior, setup, compatibility, or release flow changes, keep related docs
in sync as needed:

- README.md
- readme.txt
- CHANGELOG.md
- CONTRIBUTING.md
- AGENTS.md, CLAUDE.md, CODEX.md

Do not create release notes, version bumps, or coordination-policy changes as a
Copilot-owned decision. Suggest them for Claude Code or Codex review.
