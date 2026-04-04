# Changelog

All notable changes to this project will be documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the versioning
reflects WordPress plugin releases for Spectre Icons.

## [Unreleased]

### Added

- Added a PHPUnit harness for icon library preferences, manifest-backed
  registration, inline SVG rendering, and Elementor preview/config behavior.
- Added per-library enablement controls so individual bundled icon libraries can
  be turned on or off from the Elementor settings flow.
- Added GitHub Actions support for WordPress.org deployment and related release
  automation.
- Added repository maintenance files including `AGENTS.md` and `.editorconfig`
  to align the project with the broader PHCDevworks workflow.

### Changed

- Added runnable `composer` and `npm` test scripts plus Playwright environment
  variable support for local preview smoke testing.
- Refined the Elementor integration and manifest validation paths to keep
  registration behavior stricter, safer, and easier to extend.
- Refreshed the documentation set to better match current PHCDevworks project
  standards while keeping the repository focused on builder icon-library
  expansion.
- Updated build and deployment workflows for WordPress.org packaging, asset
  handling, and SVN release steps.

### Fixed

- Hidden icon libraries no longer remain visible in the Elementor icon modal
  after being disabled in plugin settings.
- Improved sanitization, labeling, and path hardening across manifest loading,
  library configuration, and SVG rendering code.
- Tightened WordPress.org deployment authentication and packaging behavior to
  reduce release friction.

## [1.1.0] - 2026-01-11

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

[unreleased]: https://github.com/phcdevworks/spectre-icons/compare/1.1.0...HEAD
[1.1.0]: https://github.com/phcdevworks/spectre-icons/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/phcdevworks/spectre-icons/compare/0.0.1...1.0.0
[0.0.1]: https://github.com/phcdevworks/spectre-icons/tree/0.0.1
