# Spectre Icons Agent Guide

This repository is maintained by PHCDevworks and contains the Spectre Icons
plugin product.

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

Spectre Icons is a standalone PHCDevworks product under the broader umbrella.
It is not a core Spectre ecosystem package and must not be treated as one.

It may share quality standards, naming discipline, or internal philosophies with
other PHCDevworks projects, but its mission is product-focused:

- expand icon libraries in builders
- support multiple builders over time
- provide reliable icon registration and rendering
- improve builder UX around icon selection and usage

## Core Rules

1. Keep the scope centered on icon-library expansion for builders.
2. Treat bundled icon packs as locked assets unless the repository owner gives
   explicit approval otherwise.
3. Do not turn this plugin into a general design system, component framework, or
   Spectre ecosystem bridge.
4. Prefer manifest-driven registration and integration over hardcoded builder
   logic scattered across the codebase.
5. Support current builders cleanly and make future builder support additive,
   not destructive.
6. Preserve backward compatibility for existing installs whenever reasonably
   possible.
7. Optimize for stability, maintainability, builder UX, and product growth.

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

Unless the repository owner explicitly requests it, icon pack contents are
treated as locked source assets.

Allowed work around icon packs includes:

- manifest creation or correction
- registration logic
- lookup/indexing improvements
- builder integration work
- rendering pipeline improvements
- admin UX improvements
- performance improvements that do not modify pack source assets
- support for enabling or disabling packs

## What this repository owns

This repository owns:

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

This repository does not own:

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

Support strategy should follow this shape:

1. protect existing Elementor support
2. improve internal architecture so future builders are easier to add
3. add support for other builders only when the integration can be done cleanly
4. avoid messy one-off hacks that make later builder support harder

Candidate downstream builder targets may include:

- Elementor
- Gutenberg / block editor
- Divi
- Beaver Builder
- Oxygen
- Bricks
- other builders where demand is real

Builder support should be modular wherever possible.

## Architecture Guidance

Prefer clear separation between:

- icon asset sources
- manifests
- builder adapters
- rendering logic
- admin/settings logic
- compatibility shims

Good architecture for this repository is:

- manifest-driven
- builder-aware
- modular
- stable under plugin updates
- easy to extend with additional builders
- conservative about regressions

Bad architecture for this repository is:

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

For any change touching builder integration, validate the real user path.

## Release Mindset

This is a product repository, not a playground.

Every release should protect:

- user trust
- compatibility
- icon availability
- builder reliability
- plugin reputation on WordPress.org

Prefer smaller, controlled improvements over chaotic expansion.

## Agent Decision Filter

Before making a change, ask:

1. Does this directly improve Spectre Icons as an icon-library expansion plugin?
2. Does this preserve locked icon pack assets?
3. Does this keep the repository out of broader Spectre-system drift?
4. Does this improve builder support, maintainability, stability, or UX?
5. Is this safe for a real plugin with active installs?

If the answer to these questions is not clearly yes, do not proceed.

## In one line

Spectre Icons is a focused multi-builder icon expansion product for WordPress.
Protect the icon assets, protect the scope, and improve the product without
drift.
