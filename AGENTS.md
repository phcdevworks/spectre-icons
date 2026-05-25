# Spectre Icons — Agent Guide

## AI Coordination Model

This is the central coordination document for AI agents working in this
repository. Agent-specific files may add operating details, but they must not
override the roles or hard boundaries defined here.

| Agent | Role | Primary files |
|---|---|---|
| Claude Code (`claude-sonnet-4-6`) | Lead developer | `CLAUDE.md`, `.claude/settings.json` |
| OpenAI Codex | Documentation, releases, stabilization, repo hygiene | `CODEX.md`, `.codex/*` |
| ChatGPT | Strategy, coordination, prompt design, external review | — |
| GitHub Copilot | General development assistance | `.github/copilot-instructions.md` |
| Google Jules | Small automated maintenance | `AGENTS.md` |

Human final review and commit authority rests with Bradley Potts
(brad.potts@coastdigitalgroup.com) at PHCDevworks. No AI agent creates git
commits, pushes branches, tags releases, publishes to WordPress.org, or changes
release authority.

## Agent Boundaries

Claude Code is the designated primary AI developer. All implementation work is
driven through Claude Code operating from `CLAUDE.md` as the authoritative
working guide.

OpenAI Codex keeps the repository production-ready. Codex reviews release risk,
standardizes documentation and configuration, supports changelogs and release
notes, performs targeted repo-hygiene cleanup, and stabilizes production issues
without weakening Claude Code's implementation lead.

ChatGPT provides strategy, coordination, prompt design, and external review.
ChatGPT does not own implementation work, release decisions, or commit authority.
It informs direction; Claude Code and Brad execute.

GitHub Copilot is support-only. Copilot may suggest code, tests, TypeScript
changes, APIs, and small refactors, but it does not own implementation
leadership, architecture direction, release coordination, production
stabilization, repository AI governance, or automated maintenance workflows.

Google Jules is maintenance-only. Jules may handle small fixes, dependency
updates, and micro-updates, but it must not take on large feature work,
architecture direction, release coordination, production stabilization
ownership, repository AI governance, or lead implementation decisions.

When an agent's task crosses role boundaries, hand off instead of expanding the
role. Claude resolves implementation questions. Codex resolves release,
documentation, stabilization, and configuration questions. Brad resolves final
shipping decisions.

## Agent-Specific Guides

- `CLAUDE.md` - primary development authority and implementation workflow.
- `CODEX.md` - documentation, release, stabilization, and repo hygiene workflow.
- `JULES.md` - bounded automated maintenance workflow.
- `COPILOT.md` and `.github/copilot-instructions.md` - support-assistant workflow.

## Mission

Expand native icon library support for WordPress builders through a clean,
builder-aware, manifest-driven plugin architecture.

Spectre Icons exists to solve a specific product problem:

- native WordPress and builder icon libraries are limited
- icon workflows across builders are fragmented
- site builders need access to larger, cleaner, more useful icon libraries
  without juggling multiple icon plugins

This repository should stay tightly focused on solving that problem well.

## Product Positioning

Spectre Icons is a standalone PHCDevworks product. It is not a core Spectre
ecosystem package and must not be treated as one.

It may share quality standards, naming discipline, or internal philosophies with
other PHCDevworks projects, but its mission is product-focused:

- expand icon libraries in builders
- support multiple builders over time
- provide reliable icon registration and rendering
- improve builder UX around icon selection and usage

## Core Rules

1. Keep the scope centered on icon-library expansion for builders.
2. Treat bundled icon packs as locked assets — do not modify SVG source files
   without explicit approval from Brad Potts.
3. Do not turn this plugin into a general design system, component framework, or
   Spectre ecosystem bridge.
4. Prefer manifest-driven registration and integration over hardcoded builder
   logic scattered across the codebase.
5. Support current builders cleanly and make future builder support additive,
   not destructive.
6. Preserve backward compatibility for existing installs whenever reasonably
   possible.
7. Optimize for stability, maintainability, builder UX, and product growth.
8. Align with PHCDevworks and Spectre documentation, release, naming, and
   quality standards without treating this plugin as a core Spectre ecosystem
   package.
9. Do not use weapons language or refer to Spectre as an "8-layer" system.

## Shared Edit Boundaries

These rules apply to every agent without exception.

| Path | Status | Notes |
|---|---|---|
| `spectre-icons.php` | May edit | Plugin bootstrap, version constant, require list |
| `includes/core/` | May edit | Builder-agnostic implementation |
| `includes/elementor/` | May edit | Elementor adapter and integration logic |
| `assets/manifests/*.json` | May edit | Metadata corrections only for bundled libraries; SVG payloads locked |
| `assets/css/`, `assets/js/` | May edit | Admin and editor UI assets |
| `tests/` | May edit | PHPUnit and Playwright test coverage |
| `README.md`, `readme.txt`, `CHANGELOG.md`, other docs | May edit | Keep aligned with actual plugin behavior |
| `composer.json`, `package.json`, `.github/`, `.codex/` | May edit | Config, workflow, templates, and checklists |
| `assets/iconpacks/` | Never edit directly | Bundled SVG source assets; locked unless Brad Potts explicitly approves |
| Serialization-anchored slugs and class prefixes | Never change | Changing silently breaks every icon saved on active installs |

Full validation command: `npm run check`.

## Hard Boundaries

### Do not touch icon pack SVG contents

Bundled icon pack SVG files are protected.

Agents must not:

- edit SVG source files
- rename SVG files
- delete SVG files
- regenerate SVG files
- optimize or minify SVG files
- normalize SVG markup
- rewrite paths, fills, strokes, dimensions, or viewBox values
- repackage icon pack contents
- swap one icon asset for another
- apply bulk transforms to icon libraries

Unless Brad Potts explicitly requests it, icon pack contents are treated as
locked source assets.

Allowed work around icon packs includes:

- manifest creation or correction
- registration logic
- lookup/indexing improvements
- builder integration work
- rendering pipeline improvements
- admin UX improvements
- performance improvements that do not modify pack source assets
- support for enabling or disabling packs

### Serialization-anchored library slugs

The following slugs and class prefixes are LOCKED in PHP and must never change:

| Slug                 | Class prefix      | Manifest file             |
|----------------------|-------------------|---------------------------|
| `spectre-lucide`     | `spectre-lucide-` | `spectre-lucide.json`     |
| `spectre-fontawesome`| `spectre-fa-`     | `spectre-fontawesome.json`|

Every icon saved to the database encodes the prefix in its class value
(e.g. `spectre-lucide-arrow-right`). Changing either field would silently break
every icon already placed on any site using this plugin.

## What this repository owns

- plugin bootstrap and package structure
- builder integration logic
- icon library registration
- manifest loading and validation
- icon rendering behavior
- admin controls and settings UX
- editor preview integration
- builder compatibility expansion
- packaging and release discipline for the plugin product

## What this repository does not own

- the broader Spectre system architecture
- design-token infrastructure
- component system logic outside icon concerns
- app-shell orchestration
- general UI framework delivery
- unrelated WordPress site-building features
- theme frameworks
- page builder abstractions beyond icon-library support

## Supported and Target Builder Strategy

Current priority is to maintain and strengthen active builder support.

Support strategy:

1. protect existing Elementor support
2. improve internal architecture so future builders are easier to add
3. add support for other builders only when the integration can be done cleanly
4. avoid messy one-off hacks that make later builder support harder

Candidate downstream builder targets:

- Elementor (active)
- Gutenberg / block editor
- Divi
- Beaver Builder
- Oxygen
- Bricks

Builder support should be modular wherever possible.

## Architecture Guidance

Prefer clear separation between:

- icon asset sources
- manifests
- builder adapters
- rendering logic
- admin/settings logic
- compatibility shims

Good architecture:

- manifest-driven
- builder-aware
- modular
- stable under plugin updates
- easy to extend with additional builders
- conservative about regressions

Bad architecture:

- builder-specific logic spread everywhere
- direct editing of icon assets to fix integration problems
- hidden coupling between admin UI and rendering paths
- product drift into unrelated Spectre infrastructure
- large rewrites without clear product payoff

## Change Priorities

When choosing what to work on, prioritize in this order:

1. bugs that break icon rendering or builder compatibility
2. regressions affecting existing installs
3. manifest correctness and library registration reliability
4. builder UX improvements
5. performance and maintainability improvements
6. new builder support
7. new product enhancements

## Backward Compatibility

This plugin has real users and active installs. Treat compatibility seriously.

When making changes:

- avoid breaking existing registered libraries
- avoid changing user-visible behavior without reason
- avoid disruptive admin UX changes unless the gain is clear
- make migration paths explicit where needed
- preserve stable behavior across supported WordPress and builder versions

## Performance Guidance

Performance matters, but not at the expense of correctness.

Prefer:

- lazy or targeted loading where appropriate
- manifest-driven registration
- minimal duplication
- efficient builder integration paths
- rendering strategies that do not mutate source icon assets

Do not chase performance through destructive modification of icon pack files.

## WordPress and Builder Discipline

Follow WordPress plugin standards and builder extension best practices.

Prefer:

- official extension points
- predictable hooks and lifecycle usage
- clear version compatibility handling
- minimal assumptions about builder internals
- graceful degradation where possible

Do not rely on brittle hacks when a stable integration path exists.

## Pull Request Requirements

Every agent that opens a PR must populate every section of the repo's PR
template (`.github/pull_request_template.md`):

- **Summary** - linked issue or N/A, what changed, why the change is needed.
- **Type of Change** - exactly one of: bug fix, new feature, breaking change,
  documentation only, or refactor.
- **Package Boundary Check** - confirm scope stays within Spectre Icons, no
  bundled SVG changes, locked slugs preserved, and Elementor logic contained.
- **Public API / Behavior Impact** - note any user-visible behavior change or
  migration requirement.
- **Validation** - record the command run and its result.
- **Documentation** - confirm README, readme.txt, CHANGELOG, and CONTRIBUTING
  are updated where needed.
- **Release Impact** and **Codex Review** - flag if a release review is needed.

Never submit a PR with an empty body or only the template headings unfilled.

## Testing and Validation

Before shipping meaningful changes, validate as relevant:

- plugin activation works cleanly
- icon libraries register correctly
- enabled/disabled library controls behave correctly
- builder picker integration still works
- icons render in editor and frontend
- no existing packs disappear unexpectedly
- no pack metadata is corrupted
- no builder-specific regression is introduced

PHPUnit: `composer test`
Lint: `bin/lint-php.sh` or `composer lint`
E2E (requires running WP + Elementor env): `npm run test:e2e`

## Release Mindset

This is a product repository, not a playground.

Every release should protect:

- user trust
- compatibility
- icon availability
- builder reliability
- plugin reputation on WordPress.org

Prefer smaller, controlled improvements over chaotic expansion.

Do not push to remote or publish releases without Brad Potts reviewing first.

## Agent Decision Filter

Before making a change, ask:

1. Does this directly improve Spectre Icons as an icon-library expansion plugin?
2. Does this preserve locked icon pack assets?
3. Does this preserve the serialization-anchored library slugs?
4. Does this keep the repository out of broader Spectre-system drift?
5. Does this improve builder support, maintainability, stability, or UX?
6. Is this safe for a real plugin with active installs?

If the answer to these questions is not clearly yes, do not proceed.

## In one line

Spectre Icons is a focused multi-builder icon expansion product for WordPress.
Protect the icon assets, protect the slugs, protect the scope, and improve the
product without drift.
