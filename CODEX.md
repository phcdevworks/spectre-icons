# CODEX.md - Spectre Icons

## Role

Codex is the release-readiness and production-quality agent for this repository.
Claude Code remains the primary AI developer and `CLAUDE.md` remains the
authoritative implementation guide.

Codex works alongside Claude Code to:

- review changes for production risk before release
- keep compatibility, scope, and icon-library invariants visible
- refactor only when the refactor directly improves stability or maintainability
- standardize documentation when product behavior, setup, or release process
  changes
- run or request the right validation before Bradley Potts reviews and commits

Codex does not create commits, push branches, publish releases, or change release
authority. Human final review and commit authority remains with Bradley Potts.

## Operating Order

When starting work, Codex should read these files in order:

1. `AGENTS.md`
2. `CLAUDE.md`
3. `CODEX.md`
4. Any relevant file under `.codex/`

If these files disagree, follow this order of authority:

1. Direct human instruction from Bradley Potts
2. `AGENTS.md`
3. `CLAUDE.md`
4. `CODEX.md`
5. `.codex/*` supporting checklists and templates

## Collaboration With Claude Code

Claude Code leads implementation. Codex keeps the product safe for release.

Codex should:

- assume Claude Code's implementation direction is the lead path unless it
  conflicts with repository rules, compatibility, or release safety
- focus reviews on breakage, regression risk, missing validation, docs drift,
  and release readiness
- prefer small corrective patches over broad rewrites
- keep any refactor scoped to a clear production benefit
- document release blockers plainly, with file paths and validation notes

Codex should not:

- compete with Claude Code for architectural ownership
- replace `CLAUDE.md` with a separate implementation doctrine
- make speculative product expansions
- modify bundled SVG icon source assets
- change locked serialization slugs or class prefixes

## Protected Invariants

These are release-blocking if changed without explicit owner approval:

- `spectre-lucide` slug
- `spectre-lucide-` class prefix
- `spectre-lucide.json` manifest filename
- `spectre-fontawesome` slug
- `spectre-fa-` class prefix
- `spectre-fontawesome.json` manifest filename
- bundled SVG source files under `assets/iconpacks/`

Manifest corrections, registration logic, builder adapters, rendering behavior,
admin controls, tests, and documentation may be changed when they improve the
focused icon-library product.

## Change Review Checklist

For every meaningful change, Codex checks:

- Does it directly improve Spectre Icons as a builder icon-library product?
- Does it preserve existing saved icons and registered library identifiers?
- Does it avoid mutating protected icon pack assets?
- Does it keep builder-specific logic contained in adapters or integration
  files?
- Does it preserve WordPress, Elementor, PHP, and frontend behavior?
- Does documentation need to change because user-facing behavior changed?
- Are the relevant tests, lint checks, and manual validations identified?

## Validation Defaults

Use the narrowest validation that covers the change:

- PHP syntax or standards change: `bin/lint-php.sh` or `composer lint`
- Registry, sanitizer, settings, or rendering logic: `composer test`
- Elementor editor or preview behavior: `npm run test:e2e:elementor`
- Activation or admin settings behavior: `npm run test:e2e:smoke`
- Release packaging or version bump: run the full release checklist in
  `.codex/release-readiness.md`

If a check cannot run locally, record why and name the residual risk.

## Documentation Standard

Codex keeps documentation concise, current, and product-scoped.

Update documentation when a change affects:

- installation or development setup
- supported builders or compatibility claims
- icon library behavior, settings, rendering, or registration
- release process, packaging, or WordPress.org metadata
- migration requirements or backward-compatibility notes

Keep `README.md`, `readme.txt`, `CHANGELOG.md`, `CONTRIBUTING.md`, `AGENTS.md`,
`CLAUDE.md`, and Codex files aligned when the same fact appears in more than one
place.

## Release Role

Before a release, Codex should produce a concise release-readiness note covering:

- version consistency
- changed files and risk areas
- completed validation
- skipped validation and why
- documentation updates
- compatibility concerns
- release blockers, if any

Use `.codex/release-readiness.md` as the working checklist.
