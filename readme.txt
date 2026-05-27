=== Spectre Icons ===
Contributors: phcdevworks
Tags: icons, elementor, svg, lucide, font awesome
Requires at least: 6.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.4.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds Lucide, Font Awesome, and uploaded SVG icon libraries to Elementor's icon picker.

== Description ==

Spectre Icons registers curated and uploaded SVG icon libraries inside Elementor's icon picker and renders them as inline SVGs on the frontend.

* Enable or disable individual libraries from Settings → Spectre Icons
* Upload custom SVG icons from Settings → My Icons
* Manifest-driven rendering for bundled and uploaded SVG libraries
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

Elementor 3.x and 4.x, tested through Elementor 4.x. The architecture is built to support additional builders in future releases.

= Can I disable individual icon libraries? =

Yes. Uncheck a library under Settings → Spectre Icons to hide it from the Elementor icon picker. Icons already placed on your site will continue to render.

= Can I add my own icon packs? =

Yes. Upload SVG files from Settings → My Icons. Uploaded icons are sanitized, stored in a site-specific My Icons library, and appear in supported builder icon pickers after the first upload.

= What are the system requirements? =

WordPress 6.0+, PHP 7.4+, and Elementor 3.x or 4.x tested through Elementor 4.x.

== Changelog ==

= 1.4.1 =

* Fixes manifest header discovery so library metadata does not depend on the WordPress filesystem global
* Reads only the small manifest header needed for icon library metadata instead of loading entire large manifest files during discovery
* Fixes Elementor preview config support for uploaded and external manifest_path icon libraries
* Ensures uploaded icon manifests are available before editor and frontend render calls need them
* Improves outline icon rendering by preserving inherited stroke behavior instead of forcing SVG fill or stroke values
* Adds My Icons e2e coverage for uploaded icons in the Elementor picker, editor preview, and published frontend
* Stabilizes GitHub Actions and wp-env e2e setup by pinning Elementor, activating Spectre Icons, waiting for WordPress readiness, and collecting failure logs

= 1.4.0 =

* Makes the My Icons upload library unlimited by default
* Keeps support for custom limits through the spectre_icons_user_library_limit filter
* Updates the upload page and admin JavaScript to display and enforce limits only when a numeric limit is configured

= 1.3.1 =

* Updated release metadata for the 1.3.1 maintenance release
* Preserved bundled library slugs, saved icon class prefixes, and existing icon rendering behavior

= 1.3.0 =

* Added the My Icons admin page for uploading up to 25 site-specific SVG icons
* Added the spectre-user icon library for user-uploaded icons in Elementor
* Added upload and delete controls with SVG sanitization, file-size checks, and safe manifest storage
* Kept bundled Lucide and Font Awesome libraries first in the picker before custom libraries
* Hardened custom library manifest handling with WordPress filesystem APIs
* Escaped upload delete button labels and tightened empty attribute rendering
* Added Font Awesome Elementor e2e coverage and release/version proposal tooling
* Updated compatibility metadata for WordPress 7.0 and Elementor 4.x

= 1.2.1 =

* Introduced core manifest registry and builder-agnostic SVG renderer
* Added runtime manifest auto-discovery — drop a JSON file to add a library
* Fixed icon SVG persisting in the editor after resetting or changing an icon
* Fixed Elementor editor cache invalidating on every plugin update (ver now uses manifest filemtime)
* Added automatic Elementor cache flush on first admin load after a version change
* Bundled Lucide and Font Awesome manifests are now self-describing (metadata in JSON header)

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

= 1.4.1 =
Improves manifest discovery, uploaded icon preview/render reliability, and Elementor validation stability. Existing bundled icons, uploaded icons, and saved icon classes are preserved.

= 1.4.0 =
My Icons uploads are unlimited by default. Sites using the spectre_icons_user_library_limit filter can still enforce a custom cap.

= 1.3.1 =
Maintenance release metadata update. Existing bundled-library icons, uploaded icons, and saved icon classes are preserved.

= 1.3.0 =
Adds a My Icons upload library for site-specific SVG icons. Existing bundled-library icons and saved icon classes are preserved.

= 1.2.1 =
Fixes blank icon previews in the Elementor editor after plugin updates. Existing icons on your site are not affected.

= 1.2.0 =
Adds reliable enable/disable controls for icon libraries. Existing icons on your site are not affected.

== Icon Attributions ==

= Font Awesome Free =
Licensed under CC BY 4.0 (icons) and MIT (code).
https://fontawesome.com/license/free

= Lucide Icons =
Licensed under the ISC License.
https://lucide.dev
