# Changelog

All notable changes to this project will be documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the versioning
reflects WordPress plugin releases for Spectre Icons.

## [Unreleased]

## [1.4.1] - 2026-05-27

Release Title: Manifest Reliability and E2E Stabilization

### Added

- Added Elementor settings-manager coverage so Spectre Icons settings hooks
  register independently of Elementor load timing.
- Added My Icons end-to-end coverage for uploaded SVG icons in the Elementor
  picker, editor preview, and published frontend.
- Added GitHub Actions diagnostics for Docker, WordPress readiness, plugin
  activation, and wp-env logs.

### Changed

- Improved outline icon rendering by inheriting stroke width and avoiding
  explicit SVG fill or stroke overrides.
- Pinned the Elementor test install and made local e2e setup activate Spectre
  Icons for more deterministic validation.
- Reduced the wp-env startup scope to the development environment used by the
  Playwright suite.

### Fixed

- Fixed manifest header discovery so it no longer depends on the WordPress
  filesystem global and only reads the small manifest header needed for library
  metadata.
- Fixed Elementor preview config support for `manifest_path` libraries so
  uploaded and external icon manifests stay aligned with the registration and
  frontend render paths.
- Fixed manifest availability before frontend and editor render calls so
  uploaded icon libraries are present when Elementor asks for preview data.
- Fixed flaky Elementor e2e flows by creating test pages deterministically and
  handling Elementor announcements and checklist overlays.
- Fixed plugin activation checks and WP-CLI setup commands so Elementor cannot
  break release diagnostics during CI.

## [1.4.0] - 2026-05-26

Release Title: Unlimited My Icons Uploads

### Changed

- Changed the `My Icons` upload library to be unlimited by default while
  preserving the `spectre_icons_user_library_limit` filter for sites or
  extensions that need to enforce a numeric cap.
- Updated the upload page, AJAX responses, and admin JavaScript so unlimited
  libraries display as a simple icon count and only enforce limits when a
  numeric limit is configured.

## [1.3.1] - 2026-05-25

Release Title: Maintenance Metadata Update

### Changed

- Updated plugin, package, and WordPress.org release metadata for the `1.3.1`
  maintenance release.
- Preserved existing bundled library slugs, saved icon class prefixes, and icon
  rendering behavior.

## [1.3.0] - 2026-05-25

Release Title: My Icons Uploads, Safer Manifests, and Release Tooling

### Added

- Added a builder-agnostic `Spectre_Icons_User_Library_Manager` for
  user-uploaded SVG icons, stored as a site-specific manifest under
  WordPress uploads.
- Added the `My Icons` admin page for uploading and deleting custom SVG icons
  with a default 25-icon limit.
- Added the serialization-anchored `spectre-user` library with the
  `spectre-user-` class prefix so uploaded icons can appear in supported
  builder pickers after the first upload.
- Added admin CSS and JavaScript for drag-and-drop uploads, upload status, icon
  previews, delete controls, and limit handling.
- Added Elementor support for external manifest paths and URLs so uploaded icon
  libraries flow through the same adapter and preview pipeline as bundled
  libraries.
- Added Elementor e2e coverage for Font Awesome icon picker and rendering
  behavior.
- Added release/version proposal tooling and expanded repository governance,
  PR, agent, roadmap, and release-readiness documentation.

### Changed

- Anchored bundled libraries now stay first in picker order before any
  dynamically discovered or user-uploaded libraries.
- Manifest header reads now use the WordPress filesystem API instead of direct
  file operations.
- Updated package tooling and dependency locks for the current validation and
  release workflow.
- Updated compatibility metadata for WordPress `7.0` and documented Elementor
  `4.x` support for the `1.3.0` release.

### Fixed

- Escaped uploaded-icon delete button `aria-label` output in the admin grid.
- Avoided rendering an empty attribute string when sanitized SVG attributes are
  empty.

## [1.2.1] - 2026-05-13

Release Title: Manifest Architecture, Icon Reset Fix, and Editor Stability

### Added

- Introduced `Spectre_Icons_Manifest_Registry` — a builder-agnostic static
  registry for loading and caching JSON icon manifests.
- Introduced `Spectre_Icons_Icon_Renderer` — a builder-agnostic inline SVG
  renderer that sanitizes and wraps icon output, replacing the Elementor-coupled
  manifest renderer.
- Added runtime manifest discovery: any `*.json` file dropped into
  `assets/manifests/` is automatically picked up as an icon library with no PHP
  changes required. Metadata (label, style, label_icon, class_prefix) is read
  from the manifest header.
- Added `label`, `style`, and `label_icon` metadata fields to the bundled Lucide
  and Font Awesome manifests so they are fully self-describing.
- Added automatic Elementor file cache flush on first admin load after a plugin
  version change — eliminates blank icon previews in the editor after updates.

### Changed

- Replaced hardcoded library metadata array with a serialization-anchored design:
  `manifest_file` and `class_prefix` for bundled libraries are locked in PHP
  (changing them would break existing saved icons); all display metadata is now
  sourced from the manifest JSON header.
- Library icon config version (`ver`) is now tied to the manifest file's
  modification time instead of the plugin version string. This prevents
  unnecessary Elementor editor cache invalidations on every plugin release.
- Refactored manifest path resolution into a dedicated `manifest-helpers.php`
  core helper (builder-agnostic, no Elementor dependency).
- Renamed `Spectre_Icons_Elementor_Library_Manager` to
  `Spectre_Icons_Elementor_Library_Adapter` for naming consistency.

### Fixed

- Fixed icon SVG persisting in the Elementor editor after the icon control is
  reset or changed to a different icon — a MutationObserver now calls
  `clearIconFromElement` to clear stale SVG before re-rendering.

## [1.2.0] - 2026-04-28

Release Title: Library Controls, SVG Hardening, and Test Coverage

### Added

- Hardened internal debug logging to improve observability for non-scalar messages.
- Synchronized versioning and WordPress compatibility metadata across all product manifests.
- Added per-library enablement controls so individual bundled icon libraries can
  be turned on or off from the Elementor settings flow.
- Disabled libraries are now hidden from the Elementor icon picker while existing
  icons on the site continue to render — uses JS-based tab hiding to work
  reliably with Elementor v4's React-rendered picker.
- Added Elementor version compatibility enforcement (3.0.0+).
- Added a PHPUnit harness for icon library preferences, manifest-backed
  registration, inline SVG rendering, and Elementor preview/config behavior.
- Added Playwright e2e coverage for Icon, Icon Box, Icon List, and Social Icons
  widgets across editor preview and frontend rendering paths.
- Added GitHub Actions support for WordPress.org deployment and related release
  automation.
- Added repository maintenance files including `AGENTS.md` and `.editorconfig`
  to align the project with the broader PHCDevworks workflow.
- Added defensive hardening to the icon renderer to strip event handler
  attributes from wrapper tags.
- Added support for SVG accessibility attributes (`aria-label`,
  `aria-labelledby`, `aria-describedby`) and identification (`id`) in the SVG
  sanitizer.
- Added explicit icon slug sanitization to the library manager validation path.

### Changed

- Hardened SVG sanitizer to permit local fragment identifiers in `href` and
  `xlink:href` attributes.
- Hardened SVG sanitization regex to better handle self-closing and multi-line
  tags.
- Hardened plugin bootstrap by using direct `require_once` for core includes.
- Improved attribute rendering safety in the manifest renderer.
- Improved manifest loading resilience in the renderer to handle malformed or
  unexpected manifest structures gracefully.
- Aligned icon style fallback logic in `integration-hooks.php` with the manifest
  renderer.
- Refined the Elementor integration and manifest validation paths to keep
  registration behavior stricter, safer, and easier to extend.
- Added runnable `composer` and `npm` test scripts plus Playwright environment
  variable support for local preview smoke testing.
- Updated build and deployment workflows for WordPress.org packaging, asset
  handling, and SVN release steps.
- Refreshed the documentation set to better match current PHCDevworks project
  standards while keeping the repository focused on builder icon-library
  expansion.
- Updated verified WordPress compatibility to 6.7 in plugin metadata.
- Removed legacy `window.SpectreElementorIconsConfig` JS configuration support.

### Fixed

- Hidden icon libraries no longer remain visible in the Elementor icon modal
  after being disabled in plugin settings.
- Improved settings sanitization to strictly follow the allowed library list.
- Improved sanitization, labeling, and path hardening across manifest loading,
  library configuration, and SVG rendering code.
- Tightened WordPress.org deployment authentication and packaging behavior to
  reduce release friction.

## [1.1.0] - 2026-01-11

Release Title: License Alignment and WordPress.org Stabilization

### Added

- Added SPDX license metadata to the plugin header and finalized bundled icon
  attribution documentation for Lucide and Font Awesome Free.
- Added the canonical GPL license file handling needed for plugin packaging and
  release distribution.

### Changed

- Refined Elementor manifest rendering, integration hooks, bootstrap loading,
  and SVG sanitization as part of the `1.1.0` stabilization pass.
- Updated plugin metadata, documentation formatting, and release packaging
  behavior for WordPress.org readiness.

### Fixed

- Removed production debug logging and cleaned up release packaging issues that
  affected generated ZIP output and documentation consistency.

### Removed

- Removed legacy local stub loading and obsolete licensing artifacts that were
  no longer part of the release path.

## [1.0.0] - 2026-01-10

Release Title: Stable WordPress.org Release

### Added

- Added the first stable WordPress.org-ready release of Spectre Icons with
  Elementor integration, manifest-driven library loading, admin library
  controls, preview asset loading, and automated ZIP builds.
- Added bundled Lucide and Font Awesome Free support through the manifest-based
  registration pipeline.

### Changed

- Reworked the plugin structure, Elementor integration layers, manifest
  handling, documentation, and build workflow to support a stable `1.0.0`
  release.

### Fixed

- Corrected icon prefix handling and ZIP packaging behavior so built plugin
  archives install cleanly and icons render with the expected classes.

### Removed

- Removed the old manifest-generation path and unsafe XML entity expansion in
  favor of static manifests and stricter SVG handling.

## [0.0.1] - 2025-12-10

Release Title: Initial Public Foundation

### Added

- Added the initial public foundation for Spectre Icons, including plugin
  bootstrap code, Elementor integration, bundled Lucide and Font Awesome icon
  assets, inline SVG rendering, admin assets, translation support, and
  repository governance files.
- Added PHP linting, Composer and PHPCS tooling, WordPress.org assets, and the
  first round of project documentation.

### Changed

- Iterated quickly on icon rendering, manifest handling, picker injection,
  preview behavior, CSS overrides, and codebase structure while shaping the
  first usable plugin architecture.

### Fixed

- Improved editor tooling support with updated local diagnostics and stub
  configuration for PHP development.

[unreleased]: https://github.com/phcdevworks/spectre-icons/compare/1.4.1...HEAD
[1.4.1]: https://github.com/phcdevworks/spectre-icons/compare/1.4.0...1.4.1
[1.4.0]: https://github.com/phcdevworks/spectre-icons/compare/1.3.1...1.4.0
[1.3.1]: https://github.com/phcdevworks/spectre-icons/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/phcdevworks/spectre-icons/compare/1.2.1...1.3.0
[1.2.1]: https://github.com/phcdevworks/spectre-icons/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/phcdevworks/spectre-icons/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/phcdevworks/spectre-icons/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/phcdevworks/spectre-icons/compare/0.0.1...1.0.0
[0.0.1]: https://github.com/phcdevworks/spectre-icons/tree/0.0.1
