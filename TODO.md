# TODO.md

# Spectre Icons Execution Todo

This list is aligned to the current repository and the roadmap in `ROADMAP.md`.
It is scoped to plugin stability, builder integration, icon-library expansion,
and product growth. It is not a dumping ground for speculative ideas.

---

## Phase 1 - Foundation: Completed

All Phase 1 items were delivered during the v1.0.0 through v1.5.0 release
cycle (My Icons landed in v1.3.0-v1.5.0, interleaved with the Phase 2 P0
Elementor-hardening work below).

### P0: Core Product

- [x] Manifest-driven icon library registration
  - Libraries discovered from `assets/manifests/` with no scattered
  builder-specific definitions.

- [x] Inline SVG rendering in Elementor editor and frontend
  - PHP render callback for frontend and saved views; JS MutationObserver for
  live editor preview.

- [x] Per-library enable/disable controls
  - Settings page at Settings -> Spectre Icons. Disabled libraries hide from
  the picker; existing placed icons keep rendering.

- [x] Lucide Icons library (1545 icons)
  - Bundled as a locked source asset with serialization-safe slug and prefix.

- [x] Font Awesome Free library
  - Bundled as a locked source asset with serialization-safe slug and prefix.

- [x] SVG sanitizer
  - DOM-based sanitizer with an explicit allowlist of allowed tags and
  attributes. Never bypassed for inline SVG output.

- [x] My Icons — user-uploaded SVG library (v1.3.0-v1.5.0)
  - Site-specific `spectre-user` library with `spectre-user-` prefix, admin
  upload/delete page, unlimited-by-default uploads (`spectre_icons_user_library_limit`
  filter for sites that want a cap), file-based SVG storage under the Spectre
  Icons uploads directory, and a one-time migration from the legacy `1.4.x`
  inline-manifest format.

### P1: Architecture and Safety

- [x] Builder-agnostic core separated from Elementor adapter
  - `includes/core/` stays free of page-builder imports. Elementor logic lives
  in `includes/elementor/`.

- [x] Serialization-anchored library slugs and class prefixes locked
  - Slugs and prefixes are locked in `$anchored` in `manifest-helpers.php`
  and documented in `AGENTS.md` and `CLAUDE.md`.

- [x] Elementor file cache flush on version bump
  - First admin load after a version bump flushes the Elementor cache once.
  Tracked via `spectre_icons_version` WP option.

- [x] JS icon reset fix
  - MutationObserver clears stale SVG content before re-injecting after a
  widget icon reset (v1.2.0 regression fix).

### P2: Testing and CI

- [x] PHP unit tests with no WordPress environment required
  - PHPUnit bootstrap stubs WP functions. Run with `composer test`.

- [x] Playwright E2E tests
  - Covers activation, settings, icon picker, and rendering flows. Grouped by
  product area.

- [x] CI pipeline
  - Full `npm run check` gate on every push and pull request.

- [x] Multi-agent governance docs
  - `AGENTS.md`, `CLAUDE.md`, `CODEX.md`, `COPILOT.md`, `JULES.md`,
  `ROADMAP.md`, `TODO.md` with documented roles, edit boundaries, and PR
  requirements.

---

## Requested by Downstream

Entries here are external asks from other repos, kept separate from this
repo's own self-planned queue above. See company root
[AGENTS.md](../../AGENTS.md) "Upstream Requests and Roadmap Self-Expansion"
for the convention this section follows.

- [ ] **Beaver Builder support** — requested by
      `project-design/spectre-base` (added 2026-06-27). `spectre-base` Phase 4
      P1 (Page Builder Compatibility) is gated on this repo selecting and
      shipping a Beaver Builder adapter; see
      `project-design/spectre-base/TODO.md` Phase 4 P1 and
      `project-design/spectre-base/ROADMAP.md`. This is the same item as
      Phase 2 P1 below ("Select next builder target") if Beaver Builder ends
      up being the builder selected — do not open a second, separate effort;
      resolve by selecting a builder and checking off both entries together.

---

## Phase 2 - Mature Operations

All items below are forward-looking. This phase starts from the stable v1.5.0
foundation (including the shipped My Icons upload library) and focuses on
builder hardening, multi-builder expansion, and library growth.

### P0: Elementor Integration Hardening

- [x] Close E2E coverage gaps for icon picker, rendering, and settings flows
  - Added `tests/e2e/elementor/icon-reset.spec.ts`: MutationObserver regression
  coverage (v1.2.0 fix), None-selection UI flow, settings persistence across
  reloads, and both-libraries-disabled picker verification.

- [x] Validate editor preview behavior across Elementor 3.x and 4.x
  - Code requires Elementor ≥ 3.0.0 (bootstrap guard). Known version-specific
  divergences (3.x data attributes vs 4.x text matching in `hideDisabledTabs`,
  `files_manager->clear_cache()` existence check) are handled in PHP and JS.
  The E2E suite validates against the installed version; compatibility matrix
  and version-specific code paths are documented in
  `docs/elementor-extension-points.md`.

- [x] Document Elementor-specific extension points and lifecycle dependencies
  - Created `docs/elementor-extension-points.md`: WP hooks, Elementor filters,
  enqueue hooks, JS localization contract, 3.x vs 4.x handling, and adapter
  boundary guide for a second builder.

- [x] Confirm adapter boundary is clean enough to template a second builder
  - `includes/core/` contains exactly three builder-agnostic files
  (manifest-registry, icon-renderer, manifest-helpers) with no page-builder
  imports. The Elementor adapter is fully contained in `includes/elementor/`.
  The boundary is clean.

### P1: Additional Builder Support

- [ ] Select next builder target based on demand and integration complexity
  - Candidates: Gutenberg, Bricks, Beaver Builder, Divi, Oxygen.

- [ ] Implement a new builder adapter following the Elementor adapter pattern
  - All builder-specific logic inside a new `includes/<builder>/` directory.

- [ ] Add E2E coverage for the new builder's icon picker and rendering flows

- [ ] Document the new builder's setup and compatibility requirements

### P2: Icon Library Expansion

- [ ] Evaluate candidate libraries for quality, license, and downstream demand
  - Candidates: Phosphor Icons, Tabler Icons, Heroicons.

- [ ] Add at least one new bundled library with serialization-safe slug and prefix

- [ ] Document new slugs in the anchored registry in `manifest-helpers.php`

### P3: Pro Features

- [ ] Confirm commercial delivery path with Bradley Potts before any work starts

- [x] ~~Custom icon library registration (user-supplied manifests)~~ — shipped
  free-tier as `My Icons` (v1.3.0-v1.5.0); no longer a pro candidate.

- [ ] Per-page or per-post library scoping

- [ ] Font Awesome Pro — bring-your-own-license integration
  - Approach TBD. Three realistic options:
    1. **FA Kit (CDN)** — user pastes their Kit URL from fontawesome.com into
       settings; the plugin injects the kit script. Simplest, no file hosting,
       but icons load from FA CDN (not inline SVG). Works out of the box with
       any Pro plan.
    2. **Self-hosted manifest upload** — user downloads their FA Pro package,
       runs a bundled CLI/script to generate a Spectre-format manifest, then
       uploads it via the existing My Icons upload flow or a dedicated importer.
       Keeps everything inline SVG and self-hosted. Requires a one-time export
       step from the user.
    3. **FA GraphQL API** — user provides their FA Pro API token; the plugin
       queries FA's API to fetch icon SVGs on demand. Fully automated but
       requires server-side HTTP requests and token storage.
  - Decision needed: which approach fits the product and user skill level.
  - FA Pro icons are licensed — never bundle them; user must supply their own
    credentials or package. Serialization slug and prefix must be locked before
    any icon data is saved to the database.

---

## Recommended Execution Order

1. Elementor hardening - close test gaps and confirm adapter boundary before
   adding a second builder.
2. Additional builder support - validate multi-builder architecture with a
   second adapter.
3. Icon library expansion - grow catalog after the builder model is proven.
4. Pro features - only after free-tier product is mature and commercial path is
   confirmed.

## Explicitly Out of Scope

- General design system or component framework work.
- Theme frameworks or site-building abstractions beyond icon-library support.
- App-shell orchestration or broader Spectre ecosystem infrastructure.
- Bulk modification or regeneration of bundled SVG source files.
- Unrelated WordPress features.
