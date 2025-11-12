# Spectre Elementor Icons

Adds Spectre-managed icon libraries to Elementor’s icon picker and renders them as inline SVGs on the frontend and in the editor.

What you get

- A WordPress settings page to toggle which Spectre icon tabs appear in Elementor
- Manifest-backed icon libraries (Lucide, Font Awesome Free) with thousands of icons
- Inline SVG rendering for crisp, theme-colored icons on any background
- Client-side preview injection in Elementor so you see the real SVGs while designing

How it works

1. SVG packs live under `assets/iconpacks/`
2. Run the generator to build JSON manifests into `assets/manifests/`
3. The plugin registers those manifests as Elementor icon libraries via filters
4. Elementor returns an icon “value”; we map that to a slug, look it up in the manifest, and output an inline `<svg>` wrapped in a `<span>` with helpful classes

Requirements

- WordPress 6.x+
- Elementor 3.x+
- PHP 7.4+ (8.x supported)

Installation (as a regular plugin)

1. Generate manifests (first-time and whenever icons change):
   - In this repo directory, run:
     - `php bin/generate-icon-manifests.php`
   - This creates `assets/manifests/spectre-lucide.json` and `assets/manifests/spectre-fontawesome.json`
2. Zip the folder (or symlink) and install it in WordPress as a plugin
3. Activate “Spectre Elementor Icons”
4. Go to Settings → Spectre Icons and toggle which icon tabs should appear

Developer quick start (in this dev container)

- Generate manifests: `php bin/generate-icon-manifests.php`
- PHP lint of all files: `find . -name '*.php' -print -exec php -l {} \;`

Project layout

- `spectre-elementor-icons.php` — plugin bootstrap and asset enqueues
- `includes/` — main classes and library registration
  - `class-spectre-elementor-icons-library-manager.php` — wires libraries into Elementor based on settings
  - `class-spectre-elementor-icons-manifest-renderer.php` — loads JSON manifests and renders inline SVG
  - `class-spectre-elementor-icons-settings.php` — admin UI and saved preferences for tab visibility
  - `spectre-elementor-icon-libraries.php` — defines Spectre libraries (Lucide, Font Awesome) and registers them
  - `class-spectre-elementor-icons-lucide.php` — optional direct Lucide helpers (file-based)
- `assets/iconpacks/` — raw SVGs organized by pack
- `assets/manifests/` — generated JSON maps of slug → svg
- `assets/js/spectre-elementor-icons-admin.js` — injects real SVGs into Elementor’s picker preview and on the page
- `assets/css/spectre-elementor-icons-admin.css` — baseline styles for consistent sizing and outline/filled modes
- `bin/generate-icon-manifests.php` — CLI to build manifests from SVG packs

Adding or updating icons

1. Drop new SVGs under the appropriate folder in `assets/iconpacks/` (Lucide or Font Awesome, or add a new folder for a new pack)
2. Re-run: `php bin/generate-icon-manifests.php`
3. (If a new pack) add a definition to `includes/spectre-elementor-icon-libraries.php` with:
   - `manifest` (the generated JSON filename)
   - `display_prefix` (short identifier shown by Elementor)
   - `class_prefix` (CSS class prefix used to identify the chosen icon slug)
   - `label_icon` (Elementor UI icon)
   - `style` (e.g., `outline` or `filled`)
   - `preview_selector` (CSS selector that matches your icon placeholders, e.g., `i[class*="your-prefix-"]`)

Troubleshooting

- Icons don’t render in the picker or frontend
  - Ensure manifests exist and are readable at `assets/manifests/*.json`
  - Open your browser dev tools → Network to confirm the JSON files are fetched (200 OK)
  - In WordPress, verify Settings → Spectre Icons has your libraries enabled
  - Confirm your placeholder elements (in Elementor) receive the class with the correct prefix, e.g., `lucide-home` or `sfa-user`
- PHP logs show “RENDER FAILED”
  - These are debug notices from `Manifest_Renderer`; make sure WP_DEBUG is enabled only in development. The log indicates we could not extract a slug or the library isn’t registered — typically a missing manifest or a wrong prefix
- Seeing duplicate script enqueues
  - The plugin currently enqueues the same JS for editor/front separately; it’s safe but can be consolidated. If you want, we can streamline to a single enqueue with one localization pass

Notes for contributors

- Keep changes small and additive; this plugin is meant to be low-risk inside established Elementor sites
- Prefer manifest-backed rendering (fast lookup with minimal PHP I/O at runtime)
- If you adjust the manifest schema, update the generator and JS accordingly

License
This repository contains third-party icon sets with their respective licenses. Please ensure usage complies with each icon pack’s terms. The plugin code is provided under a permissive license suitable for WordPress distribution.
