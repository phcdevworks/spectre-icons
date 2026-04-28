# Spectre Icons

[![GitHub issues](https://img.shields.io/github/issues/phcdevworks/spectre-icons)](https://github.com/phcdevworks/spectre-icons/issues)
[![GitHub pulls](https://img.shields.io/github/issues-pr/phcdevworks/spectre-icons)](https://github.com/phcdevworks/spectre-icons/pulls)
[![License](https://img.shields.io/github/license/phcdevworks/spectre-icons)](LICENSE)

WordPress plugin that adds Lucide and Font Awesome icon libraries to Elementor's icon picker, rendered as inline SVG.

[Contributing](CONTRIBUTING.md) | [Changelog](CHANGELOG.md) |
[Security Policy](SECURITY.md) |
[WordPress Plugin Directory](https://wordpress.org/plugins/spectre-icons/)

## Features

- Manifest-driven icon library registration — no scattered builder-specific definitions
- Inline SVG rendering in both editor preview and frontend
- Enable or disable individual icon libraries from plugin settings
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
composer update
bin/lint-php.sh
```

## Usage

Go to `Settings -> Spectre Icons` and enable the libraries you want. Then open any Elementor widget that has an icon field and choose a Spectre Icons tab from the picker.

Supported widgets include Icon, Icon Box, Icon List, and Social Icons.

## Included icon libraries

- Lucide Icons
- Font Awesome Free

The bundled SVG files are locked source assets. Registration, rendering, and admin controls can evolve but the icon files themselves are not modified as part of normal development.

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Elementor 3.x / 4.x

## Development

Lint PHP:

```bash
bin/lint-php.sh
```

Run PHPUnit:

```bash
composer test
```

Run Playwright e2e tests (requires a running WordPress + Elementor environment):

```bash
npm install
npm run test:e2e
```

Available e2e commands:

- `npm run test:e2e` — full suite
- `npm run test:e2e:smoke` — plugin activation and settings check
- `npm run test:e2e:elementor` — icon picker and rendering flows

Optional environment variables:

- `SPECTRE_E2E_BASE_URL`
- `SPECTRE_E2E_ADMIN_USER`
- `SPECTRE_E2E_ADMIN_PASSWORD`

## Contributing

Keep contributions focused on icon-library expansion for builders. Treat bundled SVG files as locked assets. See [CONTRIBUTING.md](CONTRIBUTING.md) for the full workflow.

## License

Plugin code is `GPL-2.0-or-later`. See [LICENSE](LICENSE).

Bundled icon libraries retain their upstream licenses:

- Lucide — ISC
- Font Awesome Free — icons CC BY 4.0, code MIT
