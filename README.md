# Spectre Icons

[![GitHub issues](https://img.shields.io/github/issues/phcdevworks/spectre-icons)](https://github.com/phcdevworks/spectre-icons/issues)
[![GitHub pulls](https://img.shields.io/github/issues-pr/phcdevworks/spectre-icons)](https://github.com/phcdevworks/spectre-icons/pulls)
[![License](https://img.shields.io/github/license/phcdevworks/spectre-icons)](LICENSE)

`Spectre Icons` is the standalone PHCDevworks WordPress plugin product for
expanding native icon library support in builders through manifest-driven
registration, inline SVG rendering, and builder-aware integration.

Maintained by PHCDevworks, it is focused on solving a specific product problem:
builder icon libraries are limited, fragmented, and often spread across
multiple plugins. Spectre Icons keeps icon-library expansion centralized in one
plugin while preserving clean rendering, reliable registration, and room for
additional builder support over time.

[Contributing](CONTRIBUTING.md) | [Changelog](CHANGELOG.md) |
[Security Policy](SECURITY.md) |
[WordPress Plugin Directory](https://wordpress.org/plugins/spectre-icons/)

## Key capabilities

- Registers curated icon libraries from JSON manifests instead of scattering
  builder-specific icon definitions throughout the codebase
- Integrates icon libraries directly into Elementor with builder-aware library
  registration and picker support
- Renders icons as inline SVG for frontend and editor consistency
- Supports theme-friendly color inheritance and builder color controls
- Includes branding-aware rendering support for social icon workflows
- Lets site owners enable or disable bundled icon libraries from plugin
  settings
- Keeps architecture modular so future builder support can be added cleanly

## Installation

### WordPress admin

Install from the WordPress plugin directory and activate the plugin:

1. Go to `Plugins -> Add New`
2. Search for `Spectre Icons`
3. Click `Install Now`
4. Activate the plugin
5. Open `Settings -> Spectre Icons`

### Manual install

Upload the plugin ZIP through the WordPress admin, then activate it:

1. Download the plugin ZIP
2. Go to `Plugins -> Add New -> Upload Plugin`
3. Upload the ZIP file
4. Activate the plugin
5. Open `Settings -> Spectre Icons`

### Development install

Clone the repository into your WordPress plugins directory or symlink it into
`wp-content/plugins/`, then lint the PHP before testing:

```bash
composer update
bin/lint-php.sh
```

## Quick start

### Enable icon libraries

Go to `Settings -> Spectre Icons` and enable the icon libraries you want to
expose in supported builders.

### Use icons in Elementor

Open any Elementor widget that supports icons and choose a Spectre Icons tab
from the picker.

Current builder support:

- Elementor 3.x+

Typical supported Elementor icon workflows include:

- Icon
- Icon Box
- Icon List
- Social Icons

### Color and branding behavior

Icons inherit color cleanly through builder controls and frontend styling.
Where builder workflows support it, branding-aware icon output can be used for
official social icon presentation.

## Included icon libraries

- `Lucide Icons`
- `Font Awesome Free`

The bundled packs are treated as locked source assets in this repository.
Registration, manifest handling, rendering behavior, and admin controls can
evolve, but the SVG source files themselves are not intended to be edited as
part of normal plugin work.

## What this plugin owns

- Plugin bootstrap and package structure
- Builder integration logic
- Icon library registration
- Manifest loading and validation
- Inline SVG rendering behavior
- Admin settings and library enable/disable controls
- Editor preview integration
- Compatibility improvements for supported and future builders

### Current architecture shape

The plugin is organized around these responsibilities:

- `spectre-icons.php` bootstraps the plugin and loads integration code
- `includes/elementor/` contains Elementor-specific registration, rendering,
  settings, and integration hooks
- `assets/manifests/` contains the manifest files used as the registration
  source for bundled libraries
- `assets/iconpacks/` contains the locked bundled icon assets
- `assets/js/` contains editor and frontend preview behavior
- `assets/css/` contains icon styling and color-related behavior

## What this plugin does not own

- General design-system infrastructure
- Broader Spectre system architecture
- Unrelated builder features outside icon-library support
- Theme framework behavior
- Component-library delivery outside icon concerns

Spectre Icons is a focused product. Its job is to expand builder icon libraries
cleanly, not to become a general WordPress UI framework.

## How it works

The runtime flow is intentionally simple:

1. Bundled icon packs are indexed through JSON manifests in `assets/manifests/`
2. Builder integration registers those manifests as available icon libraries
3. The renderer resolves icon slugs from registered manifests
4. Icons are output as inline SVG in supported editor and frontend contexts
5. Styles and scripts handle preview behavior, color inheritance, and builder UX

## Requirements and compatibility

- WordPress 6.0+
- PHP 7.4+
- Elementor 3.x+ for current active builder support

The plugin is designed to preserve existing Elementor support first and make
future builder support additive rather than destructive.

## Development

Lint PHP:

```bash
bin/lint-php.sh
```

Key source areas:

- `spectre-icons.php` for plugin bootstrap and core loading
- `includes/elementor/` for Elementor integration
- `includes/class-spectre-icons-svg-sanitizer.php` for SVG sanitization logic
- `assets/manifests/` for bundled library manifests
- `assets/js/` for editor and frontend injection behavior
- `assets/css/` for icon styling

### Testing

Fast PHP coverage now lives under `tests/phpunit/` and exercises the
manifest-driven plugin seams without needing a full WordPress boot:

```bash
composer update
composer test
```

That suite covers icon library preferences, manifest-backed registration,
renderer output, SVG sanitization, Elementor tab registration, and preview
asset config generation.

The Playwright smoke test remains the real editor-path check for preview
behavior:

```bash
npm install
npm run test:e2e:smoke
```

Browser coverage is organized by product area so future builder integrations
can slot in alongside Elementor cleanly:

- `tests/e2e/main/` for shared product/admin behavior
- `tests/e2e/elementor/` for Elementor-specific picker and preview flows
- `tests/e2e/support/` for shared Playwright helpers

Useful browser commands:

- `npm run test:e2e:main`
- `npm run test:e2e:elementor`
- `npm run test:e2e:smoke`

Optional Playwright environment variables:

- `SPECTRE_E2E_BASE_URL`
- `SPECTRE_E2E_ADMIN_USER`
- `SPECTRE_E2E_ADMIN_PASSWORD`

When changing builder integration or rendering behavior, validate the real user
path:

- plugin activation works cleanly
- icon libraries still register correctly
- enabled and disabled library controls behave correctly
- Elementor picker integration still works
- icons render in both editor and frontend
- no bundled libraries disappear unexpectedly

## Contributing

PHCDevworks maintains this repository as a focused WordPress plugin product.

When contributing:

- keep scope centered on icon-library expansion for builders
- treat bundled icon pack SVG files as locked source assets
- prefer manifest-driven registration over scattered hardcoded builder logic
- preserve backward compatibility where reasonably possible
- validate builder-facing behavior when changing integration code

See [CONTRIBUTING.md](CONTRIBUTING.md) for the full workflow.

## License

The plugin code is licensed under `GPL-2.0-or-later`. See [LICENSE](LICENSE).

Bundled icon libraries retain their own upstream licenses:

- `Lucide` is licensed under ISC
- `Font Awesome Free` icons are licensed under CC BY 4.0 and related code is
  licensed under MIT
