# Elementor Extension Points and Lifecycle Dependencies

Reference for any developer adapting the Spectre Icons Elementor adapter or
building a second builder adapter alongside it.

---

## Version Requirements

| Requirement | Value |
|---|---|
| Minimum Elementor | 3.0.0 |
| Enforced in | `integration-hooks.php` bootstrap guard |
| Behavior below minimum | Admin notice on Plugins screen; integration does not load |

The minimum is enforced at `spectre_icons_elementor_bootstrap()` via
`version_compare( ELEMENTOR_VERSION, '3.0.0', '<' )`. The plugin's settings
page is registered independently (priority 5 on `plugins_loaded`) so it
remains reachable even when Elementor is absent or too old.

---

## WordPress Action Hooks

### `plugins_loaded` — priority 5
**Function:** `spectre_icons_elementor_register_admin_settings()`

Registers the settings manager (`Spectre_Icons_Elementor_Settings`) early so
the admin settings page is available regardless of Elementor's load timing.
Must run before priority 20 so the settings object is ready when the bootstrap
fires.

### `plugins_loaded` — priority 20
**Function:** `spectre_icons_elementor_bootstrap()`

Main integration entry point. Checks that `elementor/loaded` has already fired;
if not, defers itself to `elementor/loaded` (priority 20) and queues a missing-
Elementor admin notice. Once Elementor is confirmed present:

1. Instantiates `Spectre_Icons_Elementor_Library_Adapter` (singleton).
2. Calls `spectre_icons_ensure_manifests_registered()` immediately so the core
   registry is populated before any `render_icon` call, even if the
   `elementor/icons_manager/additional_tabs` filter fires late or is cached.
3. Registers all downstream hooks (filter, enqueues, admin notices, cache flush).

The static `$bootstrapped` guard prevents double-initialization on the deferred
`elementor/loaded` path.

### `elementor/loaded` — priority 20 (deferred path only)
**Function:** `spectre_icons_elementor_bootstrap()` (re-entry)

Used only when Elementor has not loaded by the time `plugins_loaded` fires.
Rare in practice; mainly covers non-standard load orders.

### `elementor/init` — priority 5
**Function:** `spectre_icons_ensure_manifests_registered()`

Safety-net re-registration for user-uploaded manifests (e.g. My Icons) that may
be added after the initial bootstrap. Idempotent — already-registered slugs are
skipped.

### `elementor/init` — priority 100
**Function:** `spectre_icons_maybe_flush_elementor_cache()`

Flushes Elementor's file cache once per plugin version bump. Compares
`SPECTRE_ICONS_VERSION` against the `spectre_icons_version` WP option. On a
mismatch: writes the new version first (to prevent loops on fatal), then calls
`\Elementor\Plugin::$instance->files_manager->clear_cache()` if the method
exists. This prevents blank icon previews in the editor after a plugin update.

### `admin_notices`
Three scoped notice functions registered by the bootstrap:
- `spectre_icons_elementor_missing_elementor_notice` — Plugins screen only;
  Elementor not active.
- `spectre_icons_elementor_old_elementor_notice` — Plugins screen only;
  Elementor below 3.0.0.
- `spectre_icons_elementor_missing_manifest_notice` — Plugins screen +
  Spectre Icons settings screen; no manifests found.

---

## Elementor Filters

### `elementor/icons_manager/additional_tabs`
**Callback:** `Spectre_Icons_Elementor_Library_Adapter::register_additional_tabs()`

The primary hook for registering custom icon libraries with Elementor's picker.
Available since Elementor 3.0.0. The adapter calls `load_libraries()` again
inside this callback (rather than relying on the constructor-time load) to avoid
stale/late filter ordering issues.

Each tab entry must satisfy Elementor's contract:

| Key | Type | Notes |
|---|---|---|
| `name` | string | Sanitized slug; must be unique across all tabs |
| `label` | string | Human-readable tab label |
| `labelIcon` | string | Must match `eicon-[a-z0-9-]+`; empty string if none |
| `manifest` | string | Absolute filesystem path to the JSON manifest |
| `prefix` | string | CSS class prefix (e.g. `spectre-lucide-`) |
| `icons` | string[] | Array of sanitized icon slugs |
| `render_callback` | callable | `[Spectre_Icons_Icon_Renderer::class, 'render_icon']` |
| `native` | bool | Always `false` for third-party libraries |
| `ver` | string | `filemtime($manifest_path)` — NOT the plugin version |

**`ver` must be `filemtime()`, not the plugin version string.** Using the
plugin version would invalidate Elementor's icon-tab cache on every release even
when the manifest has not changed, causing unnecessary cache flushes and icon
picker delays.

When a library is disabled, the adapter passes `icons => []` rather than
omitting the tab entirely. This keeps `render_callback` registered so existing
placed icons keep rendering on the frontend; it just prevents new selections.

### `spectre_icons_elementor_icon_libraries` (internal WP filter)
**Callback:** `spectre_icons_elementor_register_manifest_libraries()`

Plugin-internal filter that bridges manifest discovery to Elementor-format tab
config. The adapter applies this filter via `apply_filters(
'spectre_icons_elementor_icon_libraries', [] )`. Third-party code may hook this
filter to inject additional libraries without modifying core files.

---

## Enqueue Hooks

| Hook | Function | Purpose |
|---|---|---|
| `elementor/editor/before_enqueue_scripts` | `spectre_icons_elementor_enqueue_styles` | CSS in editor |
| `elementor/editor/before_enqueue_scripts` | `spectre_icons_elementor_enqueue_icon_scripts` | JS + localized config in editor |
| `elementor/frontend/after_enqueue_styles` | `spectre_icons_elementor_enqueue_styles` | CSS on frontend (Elementor-managed pages) |
| `elementor/preview/enqueue_styles` | `spectre_icons_elementor_enqueue_styles` | CSS in preview iframe |
| `elementor/preview/enqueue_scripts` | `spectre_icons_elementor_enqueue_icon_scripts` | JS + config in preview iframe |
| `wp_enqueue_scripts` | `spectre_icons_elementor_enqueue_preview_assets` | Fallback for `?elementor-preview` pages |

The fallback `wp_enqueue_scripts` handler checks `$_GET['elementor-preview']`
and only enqueues when the preview query param is present. This covers cases
where the Elementor preview hooks do not fire (e.g. certain Elementor versions
or caching layers).

`wp-auth-check` is dequeued inside `spectre_icons_elementor_enqueue_icon_scripts`
to prevent the WordPress heartbeat auth-check from breaking the Elementor editor
iframe communication.

---

## JS Localization Contract

`wp_localize_script( 'spectre-icons-elementor-js', 'SpectreIconsElementorConfig', ... )`
passes a `libraries` map to the editor JS. Each entry:

```js
{
  json:     string,   // Absolute URL to the manifest JSON file
  label:    string,   // Human-readable library label
  prefix:   string,   // CSS class prefix (e.g. "spectre-lucide-")
  selector: string,   // Attribute selector (e.g. '[class*="spectre-lucide-"]')
  style:    string,   // "outline" | "filled" | ""
  enabled:  boolean,  // Whether the library is enabled in settings
}
```

The JS reads this config on load and drives the MutationObserver, SVG fetch, and
disabled-tab hiding entirely from it. Any new PHP-side library that should appear
in the editor must populate this structure via
`spectre_icons_elementor_enqueue_icon_scripts()`.

---

## Elementor Version-Specific Handling

### Elementor 3.x — Tab Attributes
Tabs in the icon picker modal carry data attributes:
`data-tab`, `data-library`, `data-icon-library`, `data-name`.

The JS `hideDisabledTabs()` targets these with attribute selectors.
The PHP `spectre_icons_elementor_enqueue_styles()` injects inline CSS covering
the same set of attributes via `wp_add_inline_style`.

### Elementor 4.x — Label Text Matching
Elementor 4.x icon manager tab links (`.elementor-icons-manager__tab-link`)
may not carry data attributes. `hideDisabledTabs()` falls back to text-content
matching against the library's `label` value for these elements.

### Cache Flush API Availability
`\Elementor\Plugin::$instance->files_manager->clear_cache()` is checked for
existence before calling, so the cache flush degrades gracefully on Elementor
versions that do not expose `files_manager` or `clear_cache`.

---

## Adapter Boundary — What a Second Builder Adapter Must Account For

The `includes/elementor/` directory is entirely self-contained. The builder-
agnostic core (`includes/core/`) exposes three stable interfaces:

| File | What it provides |
|---|---|
| `class-spectre-icons-manifest-registry.php` | Static icon registry: `register_manifest()`, `get_icon_slugs()`, `get_icon_svg()`, `has_library()` |
| `class-spectre-icons-icon-renderer.php` | `render_icon( $descriptor )` — builder-facing render callback |
| `manifest-helpers.php` | `spectre_icons_get_library_definitions()`, `spectre_icons_resolve_manifest_path()` |

A second builder adapter should:

1. Hook its own builder's registration filter (analogous to
   `elementor/icons_manager/additional_tabs`).
2. Call `spectre_icons_ensure_manifests_registered()` (or
   `Spectre_Icons_Manifest_Registry::register_manifest()` directly) before any
   `render_icon` call fires.
3. Pass `[Spectre_Icons_Icon_Renderer::class, 'render_icon']` as the render
   callback. The descriptor format expected by `render_icon` is a WP-style icon
   array: `[ 'library' => $slug, 'value' => $icon_slug ]`.
4. Never call any function in `includes/elementor/` — those are Elementor-
   specific and will create a hard coupling.
5. Place all builder-specific logic inside a new `includes/<builder>/` directory.
