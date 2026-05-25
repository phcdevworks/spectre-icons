# TODO.md

# Spectre Icons Execution Todo

This list is aligned to the current repository and the roadmap in `ROADMAP.md`.
It is scoped to plugin stability, builder integration, icon-library expansion,
and product growth. It is not a dumping ground for speculative ideas.

---

## Phase 1 - Foundation: Completed

All Phase 1 items were delivered during the v1.0.0 through v1.2.1 release cycle.

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

## Phase 2 - Mature Operations

All items below are forward-looking. This phase starts from the stable v1.2.1
foundation and focuses on builder hardening, multi-builder expansion, and
library growth.

### P0: Elementor Integration Hardening

- [ ] Close E2E coverage gaps for icon picker, rendering, and settings flows
  - Identify untested paths in `tests/e2e/elementor/` and add coverage.

- [ ] Validate editor preview behavior across Elementor 3.x and 4.x
  - Confirm JS MutationObserver and PHP render paths both work across supported
  versions.

- [ ] Document Elementor-specific extension points and lifecycle dependencies
  - Record anything in `includes/elementor/` that a future builder adapter
  must account for.

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

- [ ] Custom icon library registration (user-supplied manifests)

- [ ] Per-page or per-post library scoping

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
