---
name: project-serialization-safety
description: Critical rule — never change library slug or class_prefix or icon re-attachment complaints will flood in; ver must use filemtime not plugin version
metadata:
  type: project
---

Elementor serializes icon picks as `{"library":"spectre-lucide","value":"spectre-lucide-arrow-right"}`. Two fields in this payload are permanent:

- **library slug** (`spectre-lucide`, `spectre-fontawesome`) — locked in `$anchored` in `includes/core/manifest-helpers.php`
- **class_prefix** (`spectre-lucide-`, `spectre-fa-`) — also locked in `$anchored`

Changing either field breaks every icon already placed on every site without a database migration.

**Why:** The 1.2.0 release triggered widespread re-attachment complaints. Root cause was the `ver` field in the Elementor library config using `SPECTRE_ICONS_VERSION` — every plugin version bump invalidated Elementor's editor cache, making icons appear broken until re-saved.

**Fix applied (refactor, May 2026):** `ver` now uses `filemtime($manifest_path)` in both `spectre_icons_elementor_get_icon_preview_config()` and `spectre_icons_elementor_register_manifest_libraries()`. The `ver` only changes when the manifest file is actually modified.

**How to apply:**
- Never rename library slugs or prefixes — they are anchored in PHP for a reason
- Keep `ver` = `filemtime($real)`, not plugin version
- If manifests are ever regenerated (new icons), filemtime changes automatically and Elementor refreshes correctly
