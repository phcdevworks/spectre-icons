# Spectre Icons

Spectre Icons is a WordPress plugin that adds curated icon libraries to Elementor with inline SVG rendering, manifest-driven performance, and full color customization support.

Currently ships with Elementor integration, with the architecture designed to support additional builders in the future.

## Overview

Spectre Icons delivers thousands of high-quality SVG icons directly into your WordPress page builder. Whether you're using Lucide's clean outline icons or Font Awesome's comprehensive library, Spectre Icons ensures crisp rendering, proper color inheritance, and seamless Elementor integration.

## Features

- **Curated Icon Libraries**: Lucide and Font Awesome Free included out of the box
- **Inline SVG Rendering**: Crisp, scalable icons that inherit theme colors
- **Official Branding Support**: Automatic white icons on branded backgrounds for social media
- **Custom Color Control**: Full Elementor color picker integration
- **Manifest-Driven**: Fast, optimized icon loading via JSON manifests
- **Admin Control Panel**: Toggle which icon libraries appear in Elementor
- **Live Preview**: Real-time SVG injection in Elementor's editor

## Requirements

- WordPress 6.0+
- Elementor 3.x+
- PHP 7.4+ (8.x supported)

## Installation

### From WordPress.org (Recommended)

1. Navigate to **Plugins → Add New** in your WordPress admin
2. Search for "Spectre Icons"
3. Click **Install Now** and then **Activate**
4. Go to **Settings → Spectre Icons** to configure which icon libraries appear

### Manual Installation

1. Download the plugin ZIP file
2. Upload via **Plugins → Add New → Upload Plugin**
3. Activate the plugin
4. Configure at **Settings → Spectre Icons**

### From Source (Developers)

```bash
# Generate icon manifests
php bin/generate-icon-manifests.php

# Lint PHP files
bin/lint-php.sh

# Install as plugin (symlink or copy to wp-content/plugins/)
```

## Usage

After activation, Spectre Icons integrates seamlessly with Elementor:

1. **Access Icons**: Open any Elementor widget that supports icons (Icon, Icon Box, Icon List, Social Icons)
2. **Choose Library**: Select "Lucide Icons" or "Font Awesome" from the icon picker tabs
3. **Customize Colors**: Use Elementor's color controls to customize icon colors
4. **Official Branding**: For Social Icons, use "Official Color" mode for automatic white icons on branded backgrounds

## How It Works

1. SVG icon packs live in `assets/iconpacks/`
2. Manifest generator creates optimized JSON maps in `assets/manifests/`
3. Plugin registers manifests as Elementor icon libraries
4. JavaScript injects inline SVGs for crisp rendering
5. CSS ensures proper color inheritance and branding support

## Project Structure

```
spectre-icons/
├── spectre-icons.php              # Plugin bootstrap
├── includes/elementor/            # Elementor integration
│   ├── class-*-library-manager.php    # Library registration
│   ├── class-*-manifest-renderer.php  # SVG rendering engine
│   ├── class-*-settings.php           # Admin settings
│   └── icon-libraries.php             # Library definitions
├── assets/
│   ├── iconpacks/                 # Raw SVG files
│   ├── manifests/                 # Generated JSON manifests
│   ├── js/                        # Client-side SVG injection
│   └── css/                       # Icon styling
├── bin/
│   └── generate-icon-manifests.php # Manifest generator
└── .wordpress-org/                # Plugin directory assets
```

## Adding Custom Icon Packs

### 1. Add SVG Files

Place your SVG files in a new folder under `assets/iconpacks/`:

```
assets/iconpacks/your-pack-name/
```

### 2. Generate Manifest

Run the manifest generator:

```bash
php bin/generate-icon-manifests.php
```

### 3. Register Library

Add a definition to `includes/elementor/icon-libraries.php`:

```php
'your-pack-slug' => array(
    'label'            => __( 'Your Pack Name', 'spectre-icons' ),
    'description'      => __( 'Description of your pack.', 'spectre-icons' ),
    'manifest'         => 'spectre-your-pack.json',
    'display_prefix'   => 'yourpack',
    'class_prefix'     => 'yourpack-',
    'label_icon'       => 'eicon-star',
    'style'            => 'outline', // or 'filled'
    'preview_selector' => 'i[class*="yourpack-"]',
),
```

### 4. Test

1. Go to **Settings → Spectre Icons** and enable your new library
2. Open Elementor editor and hard refresh
3. Verify icons appear in the picker with live previews
4. Test on frontend and editor canvas

## Troubleshooting

### Icons not rendering

- Verify manifests exist at `assets/manifests/*.json`
- Check Network tab in DevTools for 200 OK responses
- Confirm libraries are enabled at **Settings → Spectre Icons**
- Hard refresh the Elementor editor (Ctrl+Shift+R)

### Colors not working

- **Official branding**: Ensure you're NOT using "Custom" style mode
- **Custom colors**: Verify color picker has a value set
- Check that CSS file is loaded in browser DevTools

### PHP errors in logs

- Debug notices only appear when `WP_DEBUG` is enabled
- Check for missing manifests or incorrect library registration

## Part of the Spectre Suite

- **Spectre Tokens**: Design token foundation
- **Spectre UI**: Core styling layer
- **Spectre Icons**: Icon library for WordPress (this plugin)
- **Spectre Blocks**: WordPress block library
- **Spectre Astro**: Astro integration
- **Spectre 11ty**: Eleventy integration

## License

GPL-2.0-or-later

### Bundled Icon Libraries

- **Lucide**: ISC License (MIT-compatible) - [lucide.dev](https://lucide.dev)
- **Font Awesome Free**: CC BY 4.0 (icons) / MIT (code) - [fontawesome.com/license](https://fontawesome.com/license/free)

## Contributing

Contributions are welcome! Please:

1. Keep changes small and focused
2. Follow WordPress coding standards
3. Test with Elementor thoroughly
4. Update documentation as needed

Open an issue or submit a pull request on [GitHub](https://github.com/phcdevworks/spectre-icons).
