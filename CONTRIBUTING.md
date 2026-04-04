# Contributing to Spectre Icons

Thanks for helping improve Spectre Icons.

Spectre Icons is a focused WordPress plugin product maintained by PHCDevworks.
It exists to expand native icon-library support in builders through
manifest-driven registration, builder-aware integration, and reliable inline SVG
rendering.

## Project focus

Keep contributions centered on the product this repository owns:

- expanding icon-library support for WordPress builders
- improving manifest loading, registration, and rendering reliability
- preserving Elementor compatibility and builder UX
- making future builder support easier to add without destabilizing current
  integrations

Avoid drifting into broader design-system, theme-framework, or unrelated
WordPress feature work.

## Non-negotiables

- Treat bundled icon pack SVG files as locked source assets unless explicit
  approval has been given to modify them.
- Prefer manifest-driven and adapter-oriented changes over scattered
  builder-specific logic.
- Preserve backward compatibility whenever reasonably possible for active
  installs.
- Follow WordPress coding, sanitization, escaping, and capability-check
  expectations.
- Validate real builder behavior when touching registration, rendering, or
  settings flows.

## Development setup

Clone the repository and install PHP tooling:

```bash
git clone https://github.com/phcdevworks/spectre-icons.git
cd spectre-icons
composer install
```

Run the baseline checks:

```bash
bin/lint-php.sh
vendor/bin/phpcs --standard=WordPress spectre-icons.php includes/
```

## Key source areas

- `spectre-icons.php` for plugin bootstrap and shared constants
- `includes/elementor/` for Elementor registration, rendering, settings, and
  integration hooks
- `includes/class-spectre-icons-svg-sanitizer.php` for SVG sanitization logic
- `assets/manifests/` for bundled icon library manifests
- `assets/iconpacks/` for locked bundled icon assets
- `assets/js/` for editor and preview behavior
- `assets/css/` for icon styling and frontend/editor presentation

## Working rules

### Builder integration

- Use official WordPress and Elementor extension points where possible.
- Keep builder-specific logic contained in the relevant adapter area.
- Test both editor and frontend behavior when changing icon rendering.

### Icon libraries and manifests

- Do not edit, rename, delete, regenerate, or bulk-transform bundled SVG source
  files unless the repository owner explicitly asks for it.
- Keep manifests accurate and aligned with the libraries they represent.
- Preserve stable slugs, prefixes, and registration behavior unless a migration
  path is intentional and documented.

### Security and compatibility

- Sanitize input and escape output consistently.
- Keep manifest path handling and SVG rendering defensive.
- Maintain compatibility with WordPress `6.0+`, PHP `7.4+`, and supported
  Elementor versions unless a deliberate support-policy change is being made.

## Validation checklist

Before opening a pull request for meaningful changes, validate as relevant:

- plugin activation works cleanly
- icon libraries still register correctly
- enabled and disabled library controls behave correctly
- Elementor picker integration still works
- icons render in both editor and frontend
- no bundled libraries disappear unexpectedly
- no manifest metadata becomes corrupted
- no new PHP warnings, notices, or console regressions appear

## Pull requests

When opening a pull request:

- keep the scope tight and product-relevant
- explain the user or maintainer problem being solved
- summarize what changed
- include testing notes, especially for WordPress, PHP, and Elementor behavior
- update `README.md`, `readme.txt`, and `CHANGELOG.md` when the change affects
  user-facing behavior, release notes, or setup guidance

## Documentation

Keep documentation aligned with current PHCDevworks project standards:

- prefer concise, ownership-oriented README and changelog structure
- document real release notes instead of dumping raw commit history
- keep builder support and compatibility claims precise
- note user-visible changes and migration concerns clearly

## Community standards

This project follows the [Code of Conduct](CODE_OF_CONDUCT.md). By
contributing, you agree that contributions are provided under the
`GPL-2.0-or-later` license used by this repository.
