=== Spectre Icons ===
Contributors: spectre
Tags: icons, elementor, svg, library
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds Spectre-managed icon libraries to Elementor with inline SVG rendering, admin controls, and manifest-driven performance.

== Description ==

Spectre Icons exposes Spectre’s icon packs inside Elementor’s icon picker, renders them as inline SVGs on the frontend, and keeps everything modular so other builders (Gutenberg, etc.) can plug in later. Key features:

* Toggle individual libraries from **Settings → Spectre Icons**
* Manifest-driven SVG rendering for Lucide + Font Awesome out of the box
* Automatic inline SVG injection within Elementor, so previews and widgets match
* Cache-busted assets and translation-ready strings for WordPress.org distribution

To update icons, drop SVGs under `assets/iconpacks`, run `php bin/generate-icon-manifests.php`, and upload the resulting JSON manifests under `assets/manifests`.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` or install the ZIP via the WordPress dashboard.
2. Activate **Spectre Icons** through the “Plugins” screen.
3. Optional: run `php bin/generate-icon-manifests.php` locally if you customized icon packs.
4. Visit **Settings → Spectre Icons** and enable the icon libraries you want exposed inside Elementor.

== Frequently Asked Questions ==

= Do I need Elementor installed? =
Yes, the current integration targets Elementor. The architecture is modular, so support for other builders can be added later without breaking this plugin.

= Why do I need JSON manifests? =
Manifests keep SVG markup out of PHP files and let the plugin load icons quickly without scanning directories on every request. Generate them with the provided CLI script whenever you update icon packs.

= License =
The Spectre Icons plugin code is GPL-2.0-or-later. Bundled icon packs retain their upstream licenses:

* Lucide — ISC (MIT-compatible): https://github.com/lucide-icons/lucide
* Font Awesome Free — CC BY 4.0 for icons / MIT for code: https://fontawesome.com/license/free

== Changelog ==

= 0.1.0 =
* Initial release with Spectre Icons core, Elementor integration, and Lucide/Font Awesome libraries.

== Upgrade Notice ==

= 0.1.0 =
First release—install to add Spectre-managed icon packs to Elementor.
