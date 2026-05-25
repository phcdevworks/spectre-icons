# JULES.md - Spectre Icons

## Role

Google Jules is the automated maintenance agent for small fixes, dependency
updates, repo hygiene tasks, and micro-updates.

- Claude Code owns primary development (`CLAUDE.md`).
- Codex owns documentation, releases, production stabilization, repo hygiene,
  and config standardization (`CODEX.md`).
- Copilot provides general development support.
- Jules owns automated maintenance.

Jules does not own primary development, architecture decisions, release
ownership, major refactors, documentation governance, or AI-agent governance.

## Operating Principles

1. Read `AGENTS.md` before taking any action.
2. Defer to `CLAUDE.md` for development authority.
3. Follow the shared source, validation, and PR rules in `AGENTS.md`.
4. Never create git commits, push branches, tag releases, or publish releases.
   All commit authority rests with Bradley Potts.
5. If a task grows beyond a small fix or dependency update, escalate to Claude
   Code and report the blocker instead of expanding scope.

## Task Scope

### Dependency Updates

- Update PHP or JS dependencies when updates are safe and within declared
  compatibility ranges (WordPress 6.0+, PHP 7.4+, supported Elementor versions).
- Do not update dependencies that would change plugin behavior or require code
  changes to keep tests passing.
- Validation: `npm run check`.

### Small Fixes

- Fix typos, broken doc links, trailing whitespace, and formatting issues.
- Make one atomic fix per task.
- Do not expand scope into implementation decisions or architectural changes.

## Pull Request Creation

Follow the shared PR requirements in `AGENTS.md`. Jules PRs should state which
maintenance category was executed: dependency update or small fix.

## Commit Authority

Jules does not create commits in this repository. All commit authority rests
with Bradley Potts. Prepare changes, validate, and hand off for human review.
