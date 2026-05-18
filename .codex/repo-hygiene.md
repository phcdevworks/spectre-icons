# Codex Repo Hygiene And Stabilization Checklist

Use this for documentation cleanup, release-system cleanup, production
stabilization, and configuration standardization.

## Scope Gate

- [ ] The work supports documentation, releases, production stabilization, repo
      hygiene, changelog/release-note support, or config cleanup.
- [ ] The work does not replace Claude Code's lead implementation role.
- [ ] The work does not assign ownership or release decisions to GitHub Copilot.
- [ ] The work does not expand Google Jules beyond small fixes, dependency
      updates, and micro-updates.
- [ ] The work does not modify bundled SVG source assets or locked icon slugs.

## Documentation Hygiene

- [ ] Remove duplicated instructions unless the duplication is intentional for an
      agent-specific entry point.
- [ ] Keep shared roles and authority in `AGENTS.md`.
- [ ] Keep Claude-specific implementation guidance in `CLAUDE.md`.
- [ ] Keep Codex release, docs, stabilization, and hygiene guidance in `CODEX.md`
      and `.codex/*`.
- [ ] Keep Copilot support guidance in `.github/copilot-instructions.md`.
- [ ] Use `AGENTS.md` for Jules guidance unless an official Jules-specific file
      is introduced later.

## Release And Changelog Hygiene

- [ ] Version values stay synchronized across `spectre-icons.php`,
      `package.json`, `readme.txt`, and `CHANGELOG.md`.
- [ ] Changelog entries describe user-visible changes, compatibility notes, and
      migration concerns.
- [ ] Release-readiness notes identify validation completed, validation skipped,
      residual risk, and blockers.

## Config Hygiene

- [ ] Agent config files use the fewest files needed for the current tools.
- [ ] Prefer TypeScript config files where the toolchain supports them cleanly
      (`eslint.config.ts`, `vitest.config.ts`, `vite.config.ts`,
      `tsup.config.ts`, `typedoc.config.ts`, `astro.config.ts`,
      `playwright.config.ts`).
- [ ] Keep JavaScript or CommonJS config files only when required by the
      ecosystem or toolchain.
- [ ] Do not create duplicate config files for the same tool.
- [ ] Local-only settings are not promoted to shared workflow rules unless they
      are useful to the team.
- [ ] Shared settings do not allow commit, push, tag, release, publish, reset,
      or destructive cleanup commands for AI agents.
- [ ] VS Code Copilot settings enable repository instruction files when present.
- [ ] CI, lint, test, and release commands named in docs match `composer.json`,
      `package.json`, and `.github/workflows/*`.
- [ ] `npm run check` remains the full validation command or docs explain why a
      repo-specific alternative is required.

## Stabilization Review

- [ ] Production risk is stated plainly.
- [ ] The smallest safe fix is preferred.
- [ ] Tests or manual checks cover the affected behavior.
- [ ] Follow-up work is captured without blocking a safe patch release.
