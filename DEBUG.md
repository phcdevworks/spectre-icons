# Debug Checklist - Icon Picker Not Showing

## Current Issue

Icons show on frontend and in style panel (after clicking), but NOT in the icon picker modal.

## What We Know Works

- ✅ Frontend rendering (icons display on page)
- ✅ Style panel preview (shows after clicking style tab)
- ✅ Manifest generation (3.2MB, valid JSON)
- ✅ JavaScript loads without errors
- ✅ CSS is enqueued

## What Doesn't Work

- ❌ Icon picker modal - icons don't appear in the grid

## Debugging Steps

### 1. Check Browser Console

Open Elementor editor and check for:

```javascript
console.log(SpectreElementorIconsConfig);
```

Should show libraries object with json URLs.

### 2. Check Network Tab

When opening icon picker, look for:

- Requests to `/assets/manifests/spectre-lucide.json`
- Requests to `/assets/manifests/spectre-fontawesome.json`
- Check if they return 200 OK

### 3. Check DOM Elements

In icon picker modal, inspect elements:

```html
<i class="lucide-heart"></i>
```

Should have this structure, but SVG might be missing.

### 4. Check Elementor's Icon Manager

In browser console:

```javascript
elementor.modules.controls.Icons;
```

### 5. Verify Icon Registration

```php
// In WordPress admin, add this temporarily to functions.php:
add_action('admin_footer', function() {
    if (isset($_GET['action']) && $_GET['action'] === 'elementor') {
        ?>
        <script>
        jQuery(window).on('elementor:init', function() {
            console.log('Elementor Icons:', elementor.config.icons);
        });
        </script>
        <?php
    }
});
```

## Possible Issues

### Issue 1: Icons not registered with Elementor properly

**Check:** Filter priority might be too low
**Fix:** Increase priority on filter

### Issue 2: JavaScript selector doesn't match

**Check:** Class names in picker vs our selectors
**Fix:** Update selectors in preview config

### Issue 3: Elementor caches icon libraries

**Check:** Clear Elementor cache
**Fix:** In WordPress admin: Elementor > Tools > Regenerate CSS & Data

### Issue 4: Missing `iconType` parameter

**Check:** Elementor might need this for custom icons
**Fix:** Add to config

### Issue 5: JSON structure mismatch

**Check:** Elementor expects specific JSON format
**Fix:** Verify manifest structure

## Quick Test Commands

```bash
# Check if manifests are accessible
curl http://your-site/wp-content/plugins/spectre-elementor-icons/assets/manifests/spectre-lucide.json | head -20

# Check file permissions
ls -la /workspaces/spectre-elementor-icons/assets/manifests/

# Verify JSON validity
php -r "json_decode(file_get_contents('/workspaces/spectre-elementor-icons/assets/manifests/spectre-lucide.json')); echo json_last_error();"
```

## Next Steps

1. Open browser console in Elementor editor
2. Note exact error messages
3. Check which API endpoints are being called
4. Verify icon class names being generated
