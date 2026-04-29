# Spectre Icons

**Contributors:** spectre
**Tags:** icons, elementor, svg, library
**Requires at least:** 6.0
**Tested up to:** 6.7
**Requires PHP:** 7.4
**Stable tag:** 1.2.0
**License:** GPLv2 or later
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

Curated SVG icon libraries for Elementor with fast manifests, inline rendering, and color controls.

## Description

Spectre Icons exposes Spectre's icon packs inside Elementor's icon picker, renders them as inline SVGs on the frontend, and keeps everything modular for future builder support. Key features:

- Toggle individual libraries from **Settings → Spectre Icons**
- Manifest-driven SVG rendering for Lucide + Font Awesome
- Automatic inline SVG injection within Elementor
- Translation-ready strings and clean frontend rendering

## Installation

1. Upload the plugin folder to `/wp-content/plugins/` or install the ZIP via the WordPress dashboard.
2. Activate **Spectre Icons** through the "Plugins" screen.
3. Visit **Settings → Spectre Icons** and enable the icon libraries you want.

## Frequently Asked Questions

**Do I need Elementor installed?**
Yes, the current integration targets Elementor 3.0+ and 4.x. The architecture is modular for future builder support.

**Can I disable individual icon libraries?**
Yes. Uncheck a library under **Settings → Spectre Icons** to hide it from the Elementor icon picker. Icons already placed on your site will continue to render.

**Why does the plugin use JSON manifests?**
Manifests keep SVG markup out of PHP files and allow the plugin to load icons efficiently. The bundled manifests are locked assets — no CLI script is needed.

**Can I add my own icon packs?**
Custom icon library registration is a pro feature.

## Changelog

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

See LICENSE.md for details.
