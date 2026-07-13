# Spectre Icons

Spectre Icons is a WordPress plugin that adds bundled and uploaded SVG icon
libraries to Elementor's icon picker, with per-library enable/disable
controls. Icons render as inline SVG so they inherit theme colors through
builder color controls, and uploaded icons are sanitized and stored in a
site-specific My Icons library.

Maintained by [PHCDevworks](https://go.phcdev.co). It ships bundled SVG icon
libraries (Lucide and Font Awesome Free) and per-library enable/disable
controls for Elementor's icon picker today, with the architecture built to
support additional page builders as they're added.

## Repository Snapshot

| Field | Value |
|-------|-------|
| Project team | `project-plugins` |
| Repository role | WordPress and Elementor icon-library plugin |
| Package/artifact | `spectre-icons` |
| Current version/status | 1.5.0 |

## Standard Workflow

1. Read [AGENTS.md](AGENTS.md), then the agent-specific guide for the task.
2. Check [TODO.md](TODO.md) and [ROADMAP.md](ROADMAP.md) for current scope.
3. Make the smallest repo-local change that satisfies the task.
4. Run `npm run check` when validation is required or practical.
5. Update docs and [CHANGELOG.md](CHANGELOG.md) only when behavior, public
   contracts, or release-relevant metadata changed.

## Documentation Map

| Guide | Path |
|-------|------|
| Agent rules | [AGENTS.md](AGENTS.md) |
| Claude Code | [CLAUDE.md](CLAUDE.md) |
| Codex | [CODEX.md](CODEX.md) |
| Copilot | [COPILOT.md](COPILOT.md) |
| Jules | [JULES.md](JULES.md) |
| Roadmap | [ROADMAP.md](ROADMAP.md) |
| Todo | [TODO.md](TODO.md) |
| Changelog | [CHANGELOG.md](CHANGELOG.md) |
| Security | [SECURITY.md](SECURITY.md) |

[![CI](https://github.com/phcdevworks/spectre-icons/actions/workflows/tests.yml/badge.svg)](https://github.com/phcdevworks/spectre-icons/actions/workflows/tests.yml)
[![GitHub issues](https://img.shields.io/github/issues/phcdevworks/spectre-icons)](https://github.com/phcdevworks/spectre-icons/issues)
[![GitHub pulls](https://img.shields.io/github/issues-pr/phcdevworks/spectre-icons)](https://github.com/phcdevworks/spectre-icons/pulls)
[![License](https://img.shields.io/github/license/phcdevworks/spectre-icons)](LICENSE)

Spectre Icons adds Lucide, Font Awesome Free, and your own uploaded SVG icons to
Elementor's icon picker as inline SVG. Libraries can be enabled or disabled
individually from plugin settings. Disabled libraries are hidden from the picker
— icons already placed on your site keep rendering.

[Contributing](CONTRIBUTING.md) | [Changelog](CHANGELOG.md) |
[Security Policy](SECURITY.md) |
[WordPress Plugin Directory](https://wordpress.org/plugins/spectre-icons/)

## When to use this plugin

**Use Spectre Icons if:**

- You are building WordPress sites with Elementor and want Lucide or Font
  Awesome Free in the icon picker.
- You want icons to render as inline SVG so they inherit theme colors through
  Elementor's color controls without a separate font file.
- You want to enable or disable icon libraries individually without touching
  icons already placed on your site.
- You want a site-specific `My Icons` library for sanitized SVG uploads.

**Do not use Spectre Icons if:**

- You are not using Elementor — Gutenberg, Divi, and other builder support is
  planned but not yet available.
- You need custom icon fonts, CSS-class icon libraries, sprite-based rendering,
  or bulk third-party icon-pack management.

## Features

- Manifest-driven icon library registration — no scattered builder-specific definitions
- Inline SVG rendering in both editor preview and frontend
- Enable or disable individual icon libraries from plugin settings
- Upload custom SVG files to a site-specific `My Icons` library
- Disabled libraries are hidden from the picker; existing icons keep rendering
- Theme-friendly color inheritance through builder color controls
- Modular architecture for future builder support

## Installation

### WordPress admin

1. Go to `Plugins -> Add New`
2. Search for `Spectre Icons`
3. Click `Install Now`, then activate
4. Open `Settings -> Spectre Icons`

### Manual

1. Download the plugin ZIP
2. Go to `Plugins -> Add New -> Upload Plugin`
3. Upload, activate, then open `Settings -> Spectre Icons`

### From source

```bash
git clone https://github.com/phcdevworks/spectre-icons.git
cd spectre-icons
composer install
bin/lint-php.sh
```

## Usage

Go to `Settings -> Spectre Icons` and enable the libraries you want. Use
`Settings -> My Icons` to upload site-specific SVG icons. Then open any
Elementor widget that has an icon field and choose a Spectre Icons tab from the
picker.

Supported widgets include Icon, Icon Box, Icon List, and Social Icons.

## Included icon libraries

- Lucide Icons
- Font Awesome Free
- My Icons, a site-specific library for uploaded SVG files

The bundled SVG files are locked source assets. Registration, rendering, and admin controls can evolve but the bundled icon files themselves are not modified as part of normal development.

## Requirements

- WordPress 6.0+, tested up to 7.0
- PHP 7.4+
- Elementor 3.x / 4.x, tested through Elementor 4.x

## Development

### Local setup

```bash
git clone https://github.com/phcdevworks/spectre-icons.git
cd spectre-icons
composer install   # installs PHPUnit and PHPCS
npm install        # installs Playwright and wp-env
```

### Full validation (mirrors CI)

```bash
npm run check       # composer validate + composer test + composer lint
npm run check:full  # PHP checks + Playwright smoke, Elementor, and My Icons e2e
```

### Individual commands

```bash
bin/lint-php.sh          # PHP lint (PHPCS)
composer lint            # PHP lint via Composer script
composer test            # PHPUnit — no WordPress environment required
```

### E2E tests (requires a running WordPress + Elementor environment)

```bash
npm run test:e2e:setup      # start wp-env, install pinned Elementor, activate Spectre Icons
npm run test:e2e            # full Playwright suite
npm run test:e2e:smoke      # activation and settings check
npm run test:e2e:elementor  # icon picker and rendering flows
npm run test:e2e:my-icons   # uploaded icon picker, editor preview, and frontend flows
npm run wp-env:stop         # stop the local environment
```

Optional environment variables for remote WP targets:

- `SPECTRE_E2E_BASE_URL`
- `SPECTRE_E2E_ADMIN_USER`
- `SPECTRE_E2E_ADMIN_PASSWORD`

## Troubleshooting

**Icons don't appear in the Elementor picker after installation**
Go to Settings → Spectre Icons and confirm the library is enabled. Then go to
Elementor → Tools → Regenerate Files & Data to flush the editor cache.

**Blank icon previews in the Elementor editor after a plugin update**
The plugin flushes its editor cache on the first admin load after an update. If
blank icons persist, go to Elementor → Tools → Regenerate Files & Data.

**PHPUnit fails with class-not-found errors**
Run `composer install` to install dev dependencies before running tests.

**`npm run check` fails on `composer validate`**
Run `composer install` to bring `composer.lock` in sync before validating.

## Contributing

Keep contributions focused on icon-library expansion for builders. Treat bundled SVG files as locked assets. See [CONTRIBUTING.md](CONTRIBUTING.md) for the full workflow.

## License

Plugin code is `GPL-2.0-or-later`. See [LICENSE](LICENSE).

Bundled icon libraries retain their upstream licenses:

- Lucide — ISC
- Font Awesome Free — icons CC BY 4.0, code MIT
