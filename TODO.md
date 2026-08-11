# TODO.md

# Spectre Icons Execution Todo

This list is aligned to the current repository and the roadmap in `ROADMAP.md`.
It is scoped to plugin stability, builder integration, icon-library expansion,
and product growth. It is not a dumping ground for speculative ideas.

Phase 1 (Foundation) and Phase 2 P0 (Elementor Integration Hardening) are
complete — see [ROADMAP.md](ROADMAP.md) "Delivered Phases" for the summary and
[CHANGELOG.md](CHANGELOG.md) for release-by-release detail. This file only
tracks what is still open.

---

## Requested by Downstream

Entries here are external asks from other repos, kept separate from this
repo's own self-planned queue below. See company root
[AGENTS.md](../../AGENTS.md) "Upstream Requests and Roadmap Self-Expansion"
for the convention this section follows.

(No open downstream requests. Beaver Builder support, previously requested by
`project-design/spectre-base` on 2026-06-27, was withdrawn — `spectre-base`
is not adopting a page builder; see its `TODO.md` "Do not add page builder
... integration" note.)

---

## P1: Additional Builder Support — ON HOLD (2026-07-19)

Divi was selected as the target, then research found it has no documented,
stable icon-registration hook comparable to Elementor's `additional_tabs`.
A follow-up survey of Gutenberg, Bricks, and Oxygen found the same gap
everywhere. Decision: hold until a candidate (most likely Bricks) ships a
documented, stable registration hook. Full comparison table in
[ROADMAP.md](ROADMAP.md) P1.

- [ ] Implement a new builder adapter following the Elementor adapter pattern
  — blocked until a viable target is confirmed
- [ ] Add E2E coverage for the new builder's icon picker and rendering flows
- [ ] Document the new builder's setup and compatibility requirements

## P2: Icon Library Expansion

- [ ] Evaluate candidate libraries for quality, license, and downstream demand
  - Candidates: Phosphor Icons, Tabler Icons, Heroicons.

- [ ] Add at least one new bundled library with serialization-safe slug and prefix

- [ ] Document new slugs in the anchored registry in `manifest-helpers.php`

## P3: Pro Features

- [ ] Confirm commercial delivery path with Bradley Potts before any work starts

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
  - Custom icon library registration (user-supplied manifests) already shipped
    free-tier as `My Icons` (v1.3.0-v1.5.0); do not re-propose it here.

---

## Recommended Execution Order

1. Additional builder support - resume once a candidate builder ships a
   documented, stable icon-registration hook.
2. Icon library expansion - grow catalog; independent of builder support, can
   start any time.
3. Pro features - only after free-tier product is mature and commercial path is
   confirmed.

## Explicitly Out of Scope

- General design system or component framework work.
- Theme frameworks or site-building abstractions beyond icon-library support.
- App-shell orchestration or broader Spectre ecosystem infrastructure.
- Bulk modification or regeneration of bundled SVG source files.
- Unrelated WordPress features.
