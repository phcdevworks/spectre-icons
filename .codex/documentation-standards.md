# Codex Documentation Standards

Spectre Icons documentation should stay concise, product-scoped, and release
useful.

## Core Rules

- Document the product as a standalone PHCDevworks WordPress plugin.
- Keep claims focused on icon library expansion for builders.
- Align with PHCDevworks and Spectre documentation, release, naming, and quality
  standards, but do not describe Spectre Icons as a broader Spectre ecosystem
  package.
- Prefer practical maintainer and user guidance over architectural theory.
- Keep compatibility claims precise and easy to verify.
- Do not use weapons language.
- Do not refer to Spectre as an "8-layer" system.

## Files And Purpose

- `README.md`: GitHub-facing product overview, setup, supported behavior, and
  contributor orientation.
- `readme.txt`: WordPress.org-facing plugin description, installation, FAQ,
  changelog, and stable tag.
- `CHANGELOG.md`: release history in Keep a Changelog style.
- `CONTRIBUTING.md`: development setup, validation expectations, and contribution
  rules.
- `AGENTS.md`: repository-wide AI agent rules and hard boundaries.
- `CLAUDE.md`: authoritative guide for Claude Code implementation work.
- `CODEX.md`: Codex documentation, release, stabilization, repo-hygiene,
  changelog/release-note, and config-standardization role.
- `.github/copilot-instructions.md`: GitHub Copilot support-role and
  suggestion-boundary guidance.
- `.codex/*`: Codex checklists and templates for release readiness, change
  review, documentation standards, repo hygiene, and stabilization.

## When To Update Docs

Update docs in the same change set when:

- a new supported builder or compatibility target is added
- Elementor behavior changes
- icon registration, rendering, sanitization, or settings behavior changes
- setup, test, lint, or release commands change
- version numbers or release metadata change
- a migration path or compatibility caveat is introduced
- AI-agent roles, handoffs, or configuration files change

## README Flow

README structure should generally follow this order, adapted to the WordPress
plugin context:

1. Package or plugin name
2. One-sentence purpose
3. Where it fits in PHCDevworks and Spectre standards
4. When to use it
5. When not to use it
6. Install
7. Quick start
8. Usage examples
9. Public behavior, hooks, settings, or extension points
10. Project structure
11. Common commands
12. Validation
13. AI and automation boundaries
14. Versioning and release notes
15. Contributing
16. License

Do not over-document internals.

## Changelog Expectations

Release entries should:

- summarize user-visible behavior first
- mention compatibility and migration concerns when present
- avoid raw commit dumps
- avoid internal implementation detail unless it helps reviewers understand risk
- keep security fixes clear without exposing exploit instructions

## WordPress.org Readme Expectations

Before release, verify:

- `Stable tag:` matches the release version.
- `Tested up to:` remains accurate.
- package metadata in `package.json` and `package-lock.json` is synchronized.
- feature claims match shipped behavior.
- changelog mirrors the release highlights.
- no unsupported builder is implied as fully supported.

## Agent Documentation Alignment

When agent roles change, update all affected files together:

- `AGENTS.md`
- `CLAUDE.md`
- `CODEX.md`
- `.github/copilot-instructions.md`
- relevant `.codex/*` checklists

Claude Code remains primary unless Bradley Potts explicitly changes that rule.
Codex remains the documentation, release, production-stabilization, repo-hygiene,
changelog/release-note, and config-standardization counterpart.
Copilot remains support-only. Jules remains maintenance-only.
