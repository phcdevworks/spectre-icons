# Spectre Icons

**Contributors:** phcdevworks
**Tags:** icons, elementor, svg, lucide, font awesome
**Requires at least:** 6.0
**Tested up to:** 7.0
**Requires PHP:** 7.4
**Stable tag:** 1.5.0
**License:** GPLv2 or later
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

Adds Lucide, Font Awesome, and uploaded SVG icon libraries to Elementor's icon picker.

## Description

Spectre Icons registers curated and uploaded SVG icon libraries inside Elementor's icon picker and renders them as inline SVGs on the frontend. Key features:

- Enable or disable individual libraries from **Settings -> Spectre Icons**
- Upload custom SVG icons from **Settings -> My Icons**
- Manifest-driven rendering for bundled and uploaded SVG libraries
- Inline SVG injection in Elementor editor preview and frontend
- Disabled libraries stay hidden from the picker while existing icons keep rendering
- Theme-friendly color inheritance through Elementor color controls

## Installation

1. Upload the plugin folder to `/wp-content/plugins/` or install the ZIP via the WordPress dashboard.
2. Activate **Spectre Icons** through the "Plugins" screen.
3. Visit **Settings -> Spectre Icons** and enable the icon libraries you want.

## Frequently Asked Questions

**Do I need Elementor installed?**
Yes, the current integration targets Elementor 3.x and 4.x. The architecture is modular for future builder support.

**Can I disable individual icon libraries?**
Yes. Uncheck a library under **Settings -> Spectre Icons** to hide it from the Elementor icon picker. Icons already placed on your site will continue to render.

**Why does the plugin use JSON manifests?**
Manifests keep SVG markup out of PHP files and allow the plugin to load icons efficiently. Bundled icon source files are locked assets.

**Can I add my own icon packs?**
Yes. Upload SVG files from **Settings -> My Icons**. Uploaded icons are sanitized, stored in a site-specific My Icons library, and appear in supported builder icon pickers after the first upload.

## Changelog

### 1.5.0

- Added file-based storage for uploaded My Icons SVGs, with each icon stored as an individual sanitized .svg file.
- Added a lightweight manifest index and compiled editor manifest for uploaded icon libraries.
- Migrated existing 1.4.x inline My Icons manifests to the new storage layout on the next admin load.
- Preserved saved spectre-user-* icon classes and legacy inline manifest fallback behavior during migration.
- Removed individual uploaded SVG files when their matching My Icons entry is deleted.

### 1.4.1

- Fixed manifest header discovery and manifest_path preview support for uploaded and external icon libraries.
- Ensured uploaded icon manifests are available before editor and frontend render calls need them.
- Improved outline icon rendering by preserving inherited stroke behavior.
- Added My Icons end-to-end coverage for picker, editor preview, and frontend rendering.
- Stabilized GitHub Actions and wp-env e2e setup with pinned Elementor, plugin activation, readiness checks, and failure logs.

### 1.4.0

- Made the My Icons upload library unlimited by default.
- Kept support for custom limits through the spectre_icons_user_library_limit filter.
- Updated the upload page and admin JavaScript to display and enforce limits only when a numeric limit is configured.

### 1.3.1

- Updated release metadata for the 1.3.1 maintenance release.
- Preserved bundled library slugs, saved icon class prefixes, and existing icon rendering behavior.

### 1.3.0

- Added the My Icons admin page for site-specific SVG uploads.
- Added the spectre-user icon library for uploaded icons in Elementor.
- Added upload and delete controls with SVG sanitization, file-size checks, and safe manifest storage.
- Added Font Awesome Elementor e2e coverage and release/version proposal tooling.
- Updated compatibility metadata for WordPress 7.0 and Elementor 4.x.

### 1.2.0

- Added per-library enable/disable controls with reliable Elementor v4 picker hiding.
- Disabled libraries are hidden from the icon picker; existing icons continue to render.
- Hardened SVG sanitizer, manifest renderer, and plugin bootstrap.
- Added PHPUnit and Playwright e2e coverage for Icon, Icon Box, Icon List, and Social Icons.
- Updated WordPress compatibility to 6.7.

### 1.1.0

- Added SPDX license metadata and finalized bundled icon attribution.
- Refined Elementor manifest rendering, integration hooks, and SVG sanitization.
- Updated plugin metadata and release packaging for WordPress.org readiness.

### 1.0.0

- Fix manifest loading and icon lookup for prefixed libraries.
- Align Elementor editor config and asset enqueues.
- Ensure Lucide outline icons render correctly.
- Clean up WP.org ZIP packaging.

## License

Spectre Icons plugin code is GPL-2.0-or-later. Bundled icon packs retain their upstream licenses:

- Lucide — ISC (MIT-compatible): https://github.com/lucide-icons/lucide
- Font Awesome Free — CC BY 4.0 for icons / MIT for code: https://fontawesome.com/license/free

See LICENSE for details.
