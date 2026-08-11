# ROADMAP.md

# Spectre Icons Roadmap

Spectre Icons is a focused WordPress plugin that expands native icon-library
support for site builders. It registers curated SVG icon libraries inside
builder icon pickers and renders them as inline SVGs on the frontend. Its job
is to solve the icon-fragmentation problem cleanly, without becoming a general
design system or site-builder framework.

This document tracks what's next. For what already shipped and why, see
[CHANGELOG.md](CHANGELOG.md) (release-by-release detail) and git history —
this file does not restate delivered work.

---

## Delivered Phases

| Phase | Summary | Shipped in |
| --- | --- | --- |
| 1 | Foundation — manifest-driven registration, inline SVG rendering (editor + frontend), per-library enable/disable, Lucide (1545 icons) + Font Awesome Free bundled, DOM-based SVG sanitizer, builder-agnostic core (`includes/core/`) with Elementor adapter (`includes/elementor/`), PHPUnit + Playwright E2E, CI gate, locked serialization-anchored slugs, Elementor cache flush on version bump, multi-agent governance docs | 1.0.0–1.5.0 |
| 1 (My Icons) | `My Icons` — site-specific, unlimited-by-default user-upload library (`spectre-user` slug, `spectre-user-` prefix), file-based SVG storage, admin upload/delete page, one-time migration from the legacy `1.4.x` inline-manifest format | 1.3.0–1.5.0 |
| 2 P0 | Elementor integration hardening — closed E2E coverage gaps (icon-reset regression, None-selection, settings persistence, both-libraries-disabled), validated editor preview across Elementor 3.x/4.x, documented extension points (`docs/elementor-extension-points.md`), confirmed the `includes/core/` ↔ `includes/elementor/` adapter boundary is clean enough to template a second builder | 1.5.x |
| 2 P1 (research) | Additional-builder target selection — evaluated Divi, Gutenberg, Bricks, and Oxygen against Elementor's `additional_tabs` model; none currently ships a documented, stable icon-registration hook. Decision: hold implementation until one does (see "On Hold" below) | 1.5.x |

Full history is in [CHANGELOG.md](CHANGELOG.md).

---

## What's Next

The foundation and Elementor integration are stable and complete. Remaining
work is demand- or decision-gated — see [TODO.md](TODO.md) for the actionable
queue.

### P1: Additional Builder Support — ON HOLD (2026-07-19)

**Objective** Add support for at least one additional WordPress builder to
reduce dependency on Elementor and expand the plugin's user base.

**Why it matters** The plugin's architecture is explicitly designed for
multiple builders. Proving that design with a second integration validates the
approach and increases the plugin's value.

**Status: On hold.** Divi was selected as the target, but research before
implementation found it has no documented, stable, first-party filter for
registering third-party icon libraries into its native picker — unlike
Elementor's `additional_tabs`. Real-world Divi integrations either override
undocumented internal functions (Divi 4, fragile across updates) or ship a
separate custom module alongside Divi's picker rather than extending it (the
pattern used by Divi 5 third-party icon plugins). Neither is a stable
foundation for an adapter using the Elementor pattern.

A follow-up survey of the other three candidates found the same gap
everywhere:

| Candidate | Native picker | Documented registration hook | Rewrite risk |
|---|---|---|---|
| Divi | Font (v4) / SVG (v5) | None — internal-function override or separate module only | High (Divi 4→5 React rewrite) |
| Gutenberg | No native icon picker exists | None (only a third-party plugin's own filter) | N/A — no real target |
| Bricks | SVG-native | None shipped yet; active community request for exactly this filter, no official commitment | Low — stable core, no rewrite in progress |
| Oxygen | SVG-native | None found | High — mid-rewrite onto the Breakdance engine |

Bricks is the least-bad option (SVG-native storage, stable core, real user
demand for this exact filter) but has not shipped one. Decision: do not
commit adapter engineering time until a candidate ships a documented,
stable icon-registration API comparable to Elementor's. Periodically
re-check `academy.bricksbuilder.io/developer/hooks/filters` for Bricks
before re-opening this phase.

**Deliverables (once a viable target ships a stable API)**

- Implement a new adapter in the pattern of `includes/elementor/`, keeping all
  builder-specific logic contained in `includes/<builder>/`.
- Add E2E coverage for the new builder's icon picker and rendering flows.
- Document the new builder's setup and compatibility requirements.

**Dependency notes**

- Requires clean adapter boundary from Phase 2 P0 before starting (satisfied).
- Requires the target builder to expose a documented, stable icon-
  registration hook — not currently true for any candidate.

---

### P2: Icon Library Expansion

**Objective** Add one or more additional curated icon libraries to increase the
plugin's value without increasing maintenance complexity.

**Why it matters** More icon options increase site-builder flexibility and
reduce the need for competing plugins. Manifest-driven registration makes
adding libraries low-risk.

**Deliverables**

- Evaluate candidate libraries for quality, license compatibility, and
  downstream demand.
- Add new libraries as bundled manifests with appropriate serialization-safe
  slugs and class prefixes.
- Document any new slugs in the anchored registry.

**Candidates** (not committed):

- Phosphor Icons
- Tabler Icons
- Heroicons

---

### P3: Pro Features

**Objective** Introduce paid capabilities to support the plugin's long-term
sustainability.

**Why it matters** Free tier remains fully functional. Pro tier unlocks
features that demand ongoing investment.

**Candidate pro capabilities**:

- Per-page or per-post library scoping
- Font Awesome Pro bring-your-own-license integration (see `TODO.md` P3 for
  the three candidate approaches)

**Dependency notes**

- Requires a clear commercial delivery path from Bradley Potts before
  implementation begins.
- Custom icon library registration and user-supplied SVG import already
  shipped free-tier as `My Icons` (v1.3.0-v1.5.0) — do not re-propose these as
  pro candidates.

---

## Explicitly Out of Scope

- General design system or component framework behavior.
- Theme frameworks or site-building abstractions beyond icon-library support.
- App-shell orchestration or Spectre ecosystem infrastructure.
- Unrelated WordPress features.
- Bulk modification or regeneration of bundled SVG source files.

---

## Recommended Execution Order

1. **P1 - Additional builder** - resume once a candidate builder ships a
   documented, stable icon-registration hook.
2. **P2 - Icon library expansion** - grow the library catalog; independent of
   P1, can start any time.
3. **P3 - Pro features** - only after a commercial delivery path is confirmed
   with Bradley Potts.
