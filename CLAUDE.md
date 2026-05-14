# CLAUDE.md — Spectre Icons

## Project Identity

**Plugin:** `spectre-icons`
**Human owner:** Bradley Potts (brad.potts@coastdigitalgroup.com)
**Primary AI developer:** Claude Code (claude-sonnet-4-6)
**Plugin version:** 1.2.1
**Repo:** https://github.com/phcdevworks/spectre-icons

This file is the authoritative guide for Claude Code operating in this repository.
Read it before touching any source file.

## Commit Policy

Claude Code does not create git commits in this repository. Prepare changes,
run all validation, and leave staging, committing, tagging, and pushing to
human review.

---

## What this plugin does

Spectre Icons registers curated SVG icon libraries (Lucide, Font Awesome Free)
inside Elementor's icon picker and renders them as inline SVGs on the frontend.
Libraries can be enabled or disabled per-install from Settings → Spectre Icons.

---

## Project layout

```
spectre-icons.php                  Plugin bootstrap + require list
includes/
  class-spectre-icons-svg-sanitizer.php     DOM-based SVG sanitizer
  core/
    class-spectre-icons-manifest-registry.php  Static icon registry (builder-agnostic)
    class-spectre-icons-icon-renderer.php       Inline SVG renderer (builder-agnostic)
    manifest-helpers.php                         Discovery, path resolution, header parsing
  elementor/
    class-spectre-icons-elementor-settings.php  Admin settings page + WP options
    class-spectre-icons-elementor-library-adapter.php  Passes libraries to Elementor filter
    icon-libraries.php                           Elementor-format library builder + filter hook
    integration-hooks.php                        WP hooks, enqueues, admin notices
assets/
  css/admin/spectre-icons-admin.css   Icon styles for editor + frontend
  js/elementor/spectre-icons-elementor.js  JS icon renderer for Elementor preview
  manifests/
    spectre-lucide.json       Lucide icon library (1545 icons)
    spectre-fontawesome.json  Font Awesome Free icon library
tests/
  phpunit/   Unit tests (no real WordPress required — bootstrap stubs WP functions)
  e2e/       Playwright end-to-end tests (requires running WP + Elementor)
```

---

## Architecture summary

**Registration flow (PHP)**

1. `plugins_loaded` → `spectre_icons_elementor_bootstrap()`
2. Bootstrap fires `Spectre_Icons_Elementor_Library_Adapter::instance($settings)`
3. Adapter applies `spectre_icons_elementor_icon_libraries` filter
4. `spectre_icons_elementor_register_manifest_libraries()` handles that filter:
   - calls `spectre_icons_get_library_definitions()` to discover manifests
   - resolves and hardens each manifest path
   - registers each library with `Spectre_Icons_Manifest_Registry`
   - returns Elementor-format config array
5. Adapter validates each library and exposes them to Elementor via
   `elementor/icons_manager/additional_tabs`

**Render flow (PHP — Elementor frontend/editor)**

`Spectre_Icons_Icon_Renderer::render_icon($icon_descriptor)` is the render
callback registered with Elementor. It:
1. Extracts library slug + icon slug from the descriptor
2. Fetches icon data from `Spectre_Icons_Manifest_Registry`
3. Sanitizes the SVG via `Spectre_Icons_SVG_Sanitizer`
4. Returns `<span class="prefix-slug style-class">SVG</span>`

**Render flow (JS — Elementor editor preview)**

The JS in `spectre-icons-elementor.js` runs in the Elementor editor iframe:
1. Reads `SpectreIconsElementorConfig.libraries` (localized via `wp_localize_script`)
2. Watches DOM via `MutationObserver` for elements matching each library's prefix selector
3. Fetches JSON manifests on demand and injects SVG via `innerHTML`
4. Handles tab hiding for disabled libraries

---

## Serialization-anchored slugs — NEVER CHANGE THESE

| Slug                  | Class prefix       | Manifest file              |
|-----------------------|--------------------|----------------------------|
| `spectre-lucide`      | `spectre-lucide-`  | `spectre-lucide.json`      |
| `spectre-fontawesome` | `spectre-fa-`      | `spectre-fontawesome.json` |

These values are baked into every icon saved to the WordPress database. Changing
them silently breaks every existing page that uses Spectre Icons. They are locked
in `manifest-helpers.php` `$anchored` array. Display metadata (label, style,
label_icon) may be updated in the manifest JSON.

---

## Adding a new icon library

Drop a new `*.json` file into `assets/manifests/`. No PHP changes required for
non-bundled libraries. The JSON must have:

```json
{
  "name": "my-library",
  "label": "My Library",
  "style": "outline",
  "label_icon": "eicon-check",
  "class_prefix": "my-lib-",
  "icons": {
    "icon-slug": "<svg ...>...</svg>"
  }
}
```

The slug is derived from the filename (e.g. `my-library.json` → `my-library`).
The class prefix defaults to `{slug}-` if not specified in the manifest header.

For bundled libraries that must be serialization-safe, add them to `$anchored`
in `spectre_icons_get_library_definitions()` with explicit slug + prefix.

---

## Manifest JSON formats supported

The registry handles three manifest shapes:

1. **Top-level icon map** (preferred):
   `{ "icons": { "arrow-right": "<svg...>", ... } }`

2. **Icon map with body only**:
   `{ "icons": { "camera": { "body": "<path .../>" } } }`
   The registry wraps body content in a standard SVG shell.

3. **Indexed list**:
   `[ { "slug": "arrow-right", "svg": "<svg...>" }, ... ]`

---

## Development commands

```bash
# PHP lint
bin/lint-php.sh
composer lint

# PHP unit tests (no WP environment needed)
composer test

# E2E tests (requires running WP + Elementor — see .wp-env.json)
npm install
npm run test:e2e            # full suite
npm run test:e2e:smoke      # activation + settings smoke test
npm run test:e2e:elementor  # icon picker + rendering flows

# Start local WP environment
npm run wp-env:start
npm run wp-env:install-elementor
```

Environment variables for e2e tests:
- `SPECTRE_E2E_BASE_URL`
- `SPECTRE_E2E_ADMIN_USER`
- `SPECTRE_E2E_ADMIN_PASSWORD`

---

## Release checklist

1. Update `SPECTRE_ICONS_VERSION` constant in `spectre-icons.php`
2. Update `Version:` in the plugin header in `spectre-icons.php`
3. Update `Stable tag:` in `readme.txt`
4. Update `package.json` `"version"` field
5. Add release entry to `CHANGELOG.md` (follow Keep a Changelog format)
6. Update comparison links at the bottom of `CHANGELOG.md`
7. Update `readme.txt` changelog section
8. Run `composer test` and `bin/lint-php.sh` — both must pass
9. Commit and have Brad Potts review before pushing or publishing

---

## Key invariants

- `ver` for Elementor library config uses `filemtime($manifest_path)`, not the
  plugin version string — this avoids invalidating Elementor's cache on every
  release when the manifest hasn't changed.
- Elementor file cache is flushed once on the first admin load after a version
  bump (tracked via `spectre_icons_version` WP option in
  `spectre_icons_maybe_flush_elementor_cache`).
- SVG sanitizer uses DOM traversal with an explicit allowlist of tags and
  attributes — never bypass it for inline SVG output.
- Admin notices are scoped to the Plugins page and the plugin settings page only.
- `update_option( ..., false )` passes `$autoload = false` — icons options should
  not autoload.

---

## Memory files

Project memory is stored in:
`~/.claude/projects/-home-phcdevone-development-phcdev-plugins-spectre-icons/memory/`

Read `MEMORY.md` in that directory for ongoing project context across sessions.

---

## Constraints summary

- Do not edit SVG files in `assets/icons/` or the icon data in the manifest JSON
- Do not rename or delete manifest files without a migration plan
- Do not change the serialization-anchored slugs or class prefixes
- Do not push to remote or publish releases without Brad Potts reviewing first
- Do not add Elementor-specific logic outside the `includes/elementor/` directory
- Builder-agnostic core (`includes/core/`) must stay free of page-builder imports
