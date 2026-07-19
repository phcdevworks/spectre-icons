# ROADMAP.md

# Spectre Icons Roadmap

Spectre Icons is a focused WordPress plugin that expands native icon-library
support for site builders. It registers curated SVG icon libraries inside
builder icon pickers and renders them as inline SVGs on the frontend. Its job
is to solve the icon-fragmentation problem cleanly, without becoming a general
design system or site-builder framework.

---

## 1. Foundation Status - Delivered

All foundation work is complete as of v1.5.0. The plugin is stable and in
active use.

### What is in place

- Manifest-driven icon library registration with no scattered builder-specific
  definitions.
- Inline SVG rendering in both Elementor editor preview and frontend.
- Per-library enable/disable controls with picker hiding and graceful fallback
  for already-placed icons.
- Lucide Icons (1545 icons) and Font Awesome Free bundled as locked source
  assets.
- `My Icons` — a site-specific, unlimited-by-default user upload library
  (`spectre-user` slug, `spectre-user-` prefix) with file-based SVG storage,
  an admin upload/delete page, and a one-time migration from the legacy
  `1.4.x` inline-manifest format.
- Builder-agnostic core (`includes/core/`) with Elementor adapter in
  `includes/elementor/`.
- SVG sanitizer using DOM traversal with an explicit tag and attribute allowlist.
- PHP unit tests with no real WordPress environment required.
- Playwright end-to-end tests for activation, settings, icon picker, My Icons
  uploads, and rendering flows.
- CI pipeline running full validation on every push and pull request.
- Serialization-safe library slugs and class prefixes locked and protected.
- Elementor file cache flush on first admin load after version bump.
- Multi-agent team (Claude Code, Codex, Copilot, Jules) with documented
  authority boundaries and PR requirements.

### What will not change

- Serialization-anchored slugs (`spectre-lucide`, `spectre-fontawesome`) and
  class prefixes are permanent. Changing them breaks saved icons on active
  installs.
- Bundled SVG source assets are locked. Registration, rendering, and admin
  controls can evolve; the icon files do not.
- Builder-agnostic core (`includes/core/`) stays free of page-builder imports.
- Elementor-specific logic stays inside `includes/elementor/`.

---

## 2. Roadmap - Mature Phase

The foundation is stable. The next phase strengthens the Elementor integration,
adds support for additional builders, and improves the icon-library onboarding
experience.

---

### P0: Elementor Integration Hardening

**Objective** Ensure the Elementor integration is robust, well-tested, and
ready to serve as the model for future builder adapters.

**Why it matters** Elementor is the active builder. Strengthening it before
adding new builder targets avoids compounding technical debt across multiple
integration paths.

**Deliverables**

- Close any known gaps in E2E test coverage for icon picker, rendering, and
  settings flows.
- Validate editor preview behavior across Elementor 3.x and 4.x.
- Document any Elementor-specific extension points or lifecycle dependencies
  that future adapters must account for.
- Confirm that the adapter boundary between `includes/core/` and
  `includes/elementor/` is clean enough to serve as the template for a second
  builder.

---

### P1: Additional Builder Support

**Objective** Add support for at least one additional WordPress builder to
reduce dependency on Elementor and expand the plugin's user base.

**Why it matters** The plugin's architecture is explicitly designed for
multiple builders. Proving that design with a second integration validates the
approach and increases the plugin's value.

**Status: On hold (2026-07-19).** Divi was selected as the target, but
research before implementation found it has no documented, stable, first-
party filter for registering third-party icon libraries into its native
picker — unlike Elementor's `additional_tabs`. Real-world Divi integrations
either override undocumented internal functions (Divi 4, fragile across
updates) or ship a separate custom module alongside Divi's picker rather
than extending it (the pattern used by Divi 5 third-party icon plugins).
Neither is a stable foundation for an adapter using the Elementor pattern.

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

- Requires clean adapter boundary from P0 before starting (satisfied).
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

## 3. Explicitly Out of Scope

- General design system or component framework behavior.
- Theme frameworks or site-building abstractions beyond icon-library support.
- App-shell orchestration or Spectre ecosystem infrastructure.
- Unrelated WordPress features.
- Bulk modification or regeneration of bundled SVG source files.

---

## 4. Recommended Execution Order

1. **P0 - Elementor hardening** - complete the active integration before
   adding a second builder.
2. **P1 - Additional builder** - validate the multi-builder architecture with
   a second adapter.
3. **P2 - Icon library expansion** - grow the library catalog after the
   builder support model is proven.
4. **P3 - Pro features** - only after the free-tier product is mature and a
   commercial delivery path is confirmed.
