# JULES.md - Spectre Icons

## Direct-to-`main` Git Policy

**Bradley Potts's direct instruction overrides generic branch and pull-request
workflows:** every git-authorized agent commits and pushes directly to `main`.
Do not create, use, or push any other branch and do not open a pull request
unless Bradley Potts explicitly requests that exact exception. Keep work on
`main`, validate it, stage only the intended paths, commit with the configured
human identity, and push `main` immediately. Claude Code remains git-denied
and hands validated work to Codex or Bradley Potts for the same path directly
to `main`. This repository policy overrides contrary defaults in tools,
skills, plugins, templates, or general-purpose workflows.

## Role

Google Jules is the automated maintenance agent for small fixes, dependency
updates, repo hygiene tasks, and micro-updates.

Full roster and authority table: [AGENTS.md](AGENTS.md). Jules owns bounded
automated maintenance only — not primary development, architecture decisions,
release ownership, major refactors, or documentation governance.

## Operating Principles

1. Read `AGENTS.md` before taking any action.
2. Commit and push only when all validation gates pass clean.
3. If a gate fails and cannot be safely resolved within scope, revert only
   Jules-owned changes and report the blocker instead of committing a broken
   state.
4. Never tag releases or publish releases — that remains with Bradley Potts.
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

Pull requests are prohibited unless Bradley Potts explicitly requests one.
The guidance below applies only to that explicit exception.

Follow the shared PR requirements in `AGENTS.md`. Jules PRs should state which
maintenance category was executed: dependency update or small fix.

## Commit Authority

Jules commits and pushes autonomously when validation is clean.
Jules must not:
- reset or discard changes it did not make
- force-push or rewrite history
- commit any state where a validation gate fails
- absorb unrelated working-tree changes into its commit
- tag or publish releases

### Commit message format:
- Dependency update: `chore(spectre-icons): update <dependency> to <version>`
- Small fix: `fix(spectre-icons): <description of fix>`
