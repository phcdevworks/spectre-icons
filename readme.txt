=== Spectre Icons ===
Contributors: phcdevworks
Tags: icons, elementor, svg, lucide, font awesome
Requires at least: 6.0
Tested up to: 6.8.3
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Modern SVG icon libraries for WordPress builders. Delivers Lucide and Font Awesome icons with performance-first architecture and seamless Elementor integration.

== Description ==

Spectre Icons brings modern SVG icon libraries like Lucide and Font Awesome directly into WordPress builders—delivering a unified, performance-first icon system that replaces dozens of fragmented icon plugins.

= Key Features =

* **Manifest-driven icon loading** for optimal performance
* **Inline SVG rendering** with theme color inheritance
* **Official branding support** for social media icons
* **Custom color control** via page builder color pickers
* **Live preview injection** in builder editors
* **Admin control panel** to toggle icon libraries
* **Modular architecture** for future builder integrations

= Current Integration =

* Elementor 3.x+ (full support)

Future releases will support Gutenberg, Beaver Builder, and other popular page builders.

= Included Icon Libraries =

* **Lucide Icons** - Modern, clean outline icons (ISC License)
* **Font Awesome Free** - Popular icon library with solid, regular, and brand styles (CC BY 4.0 icons / MIT code)

= Perfect For =

* Elementor designers who need modern icon libraries
* Developers building custom WordPress themes
* Agencies managing multiple client sites
* Anyone tired of installing multiple icon plugins

= How It Works =

1. SVG icon packs are stored in optimized JSON manifests
2. Plugin registers manifests as Elementor libraries
3. JavaScript injects inline SVGs in editor and frontend
4. CSS handles color inheritance and official branding

= Use Cases =

* Add custom icons to any Elementor widget (Icon, Icon Box, Icon List, Social Icons)
* Create branded social media icon sets with official colors
* Build custom icon layouts with full color control
* Maintain consistent icon styles across your entire site

== Installation ==

= Automatic Installation =

1. Log in to your WordPress dashboard
2. Navigate to **Plugins → Add New**
3. Search for "Spectre Icons"
4. Click **Install Now** → **Activate**
5. Navigate to **Settings → Spectre Icons** to configure

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress dashboard
3. Navigate to **Plugins → Add New → Upload Plugin**
4. Choose the ZIP file and click **Install Now**
5. Click **Activate Plugin**
6. Navigate to **Settings → Spectre Icons** to configure

= After Activation =

1. Go to **Settings → Spectre Icons**
2. Toggle which icon libraries you want to enable
3. Open the Elementor editor on any page
4. Use any icon widget and select "Lucide Icons" or "Font Awesome" from the picker tabs

== Frequently Asked Questions ==

= Which page builders are supported? =

Currently, Spectre Icons has full support for Elementor 3.x+. Future releases will add support for Gutenberg, Beaver Builder, and other popular page builders.

= How many icons are included? =

Thousands! Lucide provides over 1,300 modern outline icons, and Font Awesome Free includes 2,000+ icons across solid, regular, and brand styles.

= Can I use custom colors with the icons? =

Yes! Use Elementor's color picker to set any custom color for your icons. Icons support full theme color inheritance.

= What is "Official Color" mode? =

For social media icons, selecting "Official Color" mode automatically renders white icons on properly branded backgrounds (Facebook blue, Twitter blue, etc.) according to brand guidelines.

= Do the icons work on the frontend? =

Yes! Icons render as inline SVGs on both the Elementor editor and the frontend, ensuring consistent display and performance.

= Are the icons optimized for performance? =

Absolutely. Icons are stored in optimized JSON manifests and loaded on-demand. Only the icons you use are rendered as inline SVGs.

= Can I add my own custom icon packs? =

Yes! The plugin architecture supports custom icon libraries. Add your SVG files to `assets/iconpacks/` and register them in the plugin. See the documentation on GitHub for detailed instructions.

= Are there any conflicts with other icon plugins? =

Spectre Icons is designed to work alongside other icon plugins. However, you can disable libraries you don't need to keep your icon picker clean.

= Does this work with WooCommerce? =

Yes! Spectre Icons works anywhere Elementor can be used, including WooCommerce product pages and shop layouts.

= What are the system requirements? =

* WordPress 6.0 or higher
* PHP 7.4 or higher (PHP 8.x supported)
* Elementor 3.x or higher

== Screenshots ==

1. Settings panel - Enable/disable icon libraries
2. Elementor icon picker - Lucide Icons library
3. Elementor icon picker - Font Awesome library
4. Icon widget with custom colors
5. Social Icons widget with official branding
6. Icon List widget in action

== Changelog ==

= 1.0.0 - 2026-01-10 =

**Added**
* GitHub Actions workflow for building WP ZIP
* Manifest file fallback logic for icon libraries
* Style class to icons based on library slug

**Changed**
* Refactored build workflow and migrated readme to Markdown
* Refactored WP.org ZIP build to use rsync and improve cleanup
* Simplified WP zip build workflow and file copying
* Refactored Elementor integration and manifest handling
* Refactored plugin structure and updated metadata
* Refactored SVG sanitizer for stricter, simpler sanitization
* Refactored Elementor integration hooks for Spectre Icons
* Refactored icon library registration for Elementor
* Refactored Elementor settings class for icon libraries
* Refactored Elementor icon manifest renderer
* Refactored Spectre icon library manager for clarity and validation

**Fixed**
* Icon prefix handling and improved style overrides
* Zip build to avoid nested plugin folder

**Documentation**
* Revised and expanded README for Spectre Icons plugin

= 0.0.1 - 2025-12-10 =

**Added**
* Initial release
* Full Elementor integration
* Lucide Icons library (1,300+ icons)
* Font Awesome Free library (2,000+ icons)
* Admin settings panel
* SVG sanitization
* Custom color support
* Official branding support for social icons
* Live preview injection in Elementor editor
* Translation support
* Comprehensive documentation

== Upgrade Notice ==

= 1.0.0 =
Major stable release with improved architecture, better performance, and enhanced icon rendering. Recommended for all users.

== Additional Info ==

= Bundled Icon Libraries =

* **Lucide**: ISC License (MIT-compatible) - [lucide.dev](https://lucide.dev)
* **Font Awesome Free**: CC BY 4.0 (icons) / MIT (code) - [fontawesome.com/license/free](https://fontawesome.com/license/free)

= Part of the Spectre Suite =

* Spectre Tokens – Design token foundation
* Spectre UI – Core styling layer
* Spectre Icons – Icon library for WordPress (this plugin)
* Spectre Blocks – WordPress block library
* Spectre Astro – Astro integration
* Spectre 11ty – Eleventy integration

= Contributing =

Contributions are welcome! Visit our GitHub repository for detailed contribution guidelines:
[github.com/phcdevworks/spectre-icons](https://github.com/phcdevworks/spectre-icons)

= Support =

For issues, questions, or feature requests:
* GitHub Issues: [github.com/phcdevworks/spectre-icons/issues](https://github.com/phcdevworks/spectre-icons/issues)
* Documentation: [github.com/phcdevworks/spectre-icons](https://github.com/phcdevworks/spectre-icons)

= Credits =

Developed by [PHCDevworks](https://go.phcdev.co)

Icon libraries:
* Lucide by Lucide Contributors
* Font Awesome by Fonticons, Inc.
