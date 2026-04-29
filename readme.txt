=== Spectre Icons ===
Contributors: phcdevworks
Tags: icons, elementor, svg, lucide, font awesome
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds Lucide and Font Awesome icon libraries to Elementor's icon picker, rendered as inline SVG.

== Description ==

Spectre Icons registers curated SVG icon libraries inside Elementor's icon picker and renders them as inline SVGs on the frontend.

* Enable or disable individual libraries from Settings → Spectre Icons
* Manifest-driven rendering for Lucide and Font Awesome Free
* Inline SVG injection in editor preview and frontend
* Disabled libraries are hidden from the picker; existing icons keep rendering
* Theme-friendly color inheritance through builder color controls

Supported widgets: Icon, Icon Box, Icon List, Social Icons.

== Installation ==

= From the WordPress admin =

1. Go to Plugins → Add New
2. Search for "Spectre Icons"
3. Click Install Now, then Activate
4. Go to Settings → Spectre Icons and enable the libraries you want

= Manual install =

1. Download the plugin ZIP
2. Go to Plugins → Add New → Upload Plugin
3. Upload the ZIP, activate it, then go to Settings → Spectre Icons

== Frequently Asked Questions ==

= Which page builders are supported? =

Elementor 3.x and 4.x. The architecture is built to support additional builders in future releases.

= Can I disable individual icon libraries? =

Yes. Uncheck a library under Settings → Spectre Icons to hide it from the Elementor icon picker. Icons already placed on your site will continue to render.

= Can I add my own icon packs? =

Custom icon library registration is a pro feature.

= What are the system requirements? =

WordPress 6.0+, PHP 7.4+, and Elementor 3.x or 4.x.

== Changelog ==

= 1.2.0 =

* Added per-library enable/disable controls with reliable Elementor v4 picker hiding
* Disabled libraries are hidden from the icon picker; existing placed icons keep rendering
* Hardened SVG sanitizer, manifest renderer, and plugin bootstrap
* Added PHPUnit and Playwright e2e test coverage
* Updated WordPress compatibility to 6.7

= 1.1.0 =

* Added SPDX license metadata and finalized bundled icon attribution
* Refined Elementor manifest rendering, integration hooks, and SVG sanitization
* Updated plugin metadata and release packaging for WordPress.org

= 1.0.0 =

* Fixed manifest loading and icon lookup for prefixed libraries
* Aligned Elementor editor config and asset enqueues
* Ensured Lucide outline icons render correctly
* Cleaned up WordPress.org ZIP packaging

== Upgrade Notice ==

= 1.2.0 =
Adds reliable enable/disable controls for icon libraries. Existing icons on your site are not affected.

== Icon Attributions ==

= Font Awesome Free =
Licensed under CC BY 4.0 (icons) and MIT (code).
https://fontawesome.com/license/free

= Lucide Icons =
Licensed under the ISC License.
https://lucide.dev
