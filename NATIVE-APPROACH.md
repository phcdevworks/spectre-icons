# Native Elementor Implementation

## What We're Using

**100% native Elementor icon system** - no custom JavaScript, no iframe hacks, no workarounds.

## How It Works

### 1. Icon Registration (PHP)

```php
'config' => [
    'name'            => 'spectre-lucide',
    'label'           => 'Lucide Icons',
    'labelIcon'       => 'eicon-star',
    'displayPrefix'   => 'lucide',
    'prefix'          => 'lucide-',
    'icons'           => ['icon-name-1', 'icon-name-2', ...], // Array of slugs
    'native'          => false,
    'render_callback' => ['Spectre_Elementor_Icons_Manifest_Renderer', 'render_icon'],
    'ver'             => '0.1.0',
]
```

### 2. Icon Picker (Elementor Native)

- Elementor's React-based picker reads the `icons` array
- Displays each icon with `displayPrefix + prefix + icon-name` as className
- User clicks to select
- Selection saved to widget data as `{library: 'spectre-lucide', value: 'lucide-home'}`

### 3. Icon Rendering (Our PHP render_callback)

When Elementor needs to display a selected icon:

1. Calls `render_icon($icon, $attributes, $tag)`
2. We extract slug from `$icon['value']`
3. Load SVG from JSON manifest
4. Return `<span class="lucide lucide-home spectre-icon--rendered">...SVG...</span>`

## What We Removed

❌ No JavaScript for preview injection
❌ No iframe observers
❌ No fetchJson client-side loading
❌ No MutationObserver watching DOM
❌ No jQuery dependencies
❌ No complex event listeners

## What We Keep

✅ PHP render_callback (core functionality)
✅ CSS for icon styling (optional, for outline/filled styles)
✅ JSON manifests with SVG data
✅ Settings page for enable/disable

## Testing

1. Clear browser cache completely
2. WordPress admin → Elementor → Tools → Regenerate CSS & Data
3. Hard refresh editor (Ctrl+Shift+R)
4. Open icon picker
5. Icons should display in picker using Elementor's native rendering
6. Select icon → Elementor calls our render_callback → SVG appears

## Why This Works

Elementor's `icon-library.js` **already handles everything**:

- Normalizes icon data from the `icons` array
- Renders picker UI with React
- Manages icon selection
- Calls render_callback when rendering

We just provide the data and the callback. That's it.
