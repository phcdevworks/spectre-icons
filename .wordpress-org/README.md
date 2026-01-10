# Spectre Icons

**Contributors:** spectre
**Tags:** icons, elementor, svg, library
**Requires at least:** 6.0
**Tested up to:** 6.9
**Requires PHP:** 7.4
**Stable tag:** 1.0.0
**License:** GPLv2 or later
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html

Spectre Icons adds curated icon libraries to Elementor with inline SVG rendering, color controls, and manifest-driven performance.

## Description

Spectre Icons exposes Spectre’s icon packs inside Elementor’s icon picker, renders them as inline SVGs on the frontend, and keeps everything modular for future builder support. Key features:

- Toggle individual libraries from **Settings → Spectre Icons**
- Manifest-driven SVG rendering for Lucide + Font Awesome
- Automatic inline SVG injection within Elementor
- Translation-ready strings and clean frontend rendering

## Installation

1. Upload the plugin folder to `/wp-content/plugins/` or install the ZIP via the WordPress dashboard.
2. Activate **Spectre Icons** through the “Plugins” screen.
3. (Optional) Run `php bin/generate-icon-manifests.php` if you customize icon packs.
4. Visit **Settings → Spectre Icons** and enable the icon libraries you want.

## Frequently Asked Questions

**Do I need Elementor installed?**
Yes, the current integration targets Elementor. The architecture is modular for future support.

**Why do I need JSON manifests?**
Manifests keep SVG markup out of PHP files and let the plugin load icons quickly. Generate them with the provided CLI script whenever you update icon packs.

## Changelog

### 1.0.0

- Fix manifest loading and icon lookup for prefixed libraries.
- Align Elementor editor config and asset enqueues.
- Ensure Lucide outline icons render correctly.
- Clean up WP.org ZIP packaging.

### 0.1.0

- Initial release with Spectre Icons core, Elementor integration, and Lucide/Font Awesome libraries.

## License

Spectre Icons plugin code is GPL-2.0-or-later. Bundled icon packs retain their upstream licenses:

- Lucide — ISC (MIT-compatible): https://github.com/lucide-icons/lucide
- Font Awesome Free — CC BY 4.0 for icons / MIT for code: https://fontawesome.com/license/free

See LICENSE.md for details.
