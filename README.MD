# Spectre Icons

Spectre Icons brings modern SVG icon libraries like Lucide and Font Awesome directly into WordPress builders—delivering a unified, performance-first icon system that replaces dozens of fragmented icon plugins.

> 📋 **[View Roadmap](https://github.com/phcdevworks/spectre-icons/blob/main/ROADMAP.md)** | 🤝 **[Contributing Guide](CONTRIBUTING.md)** | 📝 **[Changelog](CHANGELOG.md)**

## Overview

`Spectre Icons` is a WordPress plugin that integrates curated icon libraries directly into page builders. Currently ships with full **Elementor integration**, with architecture designed to support Gutenberg, Beaver Builder, and other builders in future releases. It consumes thousands of SVG icons from Lucide and Font Awesome Free, delivering them as inline SVGs with proper color inheritance, official branding support, and live preview injection.

- ✅ Manifest-driven icon loading for optimal performance
- ✅ Inline SVG rendering with theme color inheritance
- ✅ Official branding support for social media icons
- ✅ Custom color control via page builder color pickers
- ✅ Live preview injection in builder editors
- ✅ Admin control panel to toggle icon libraries
- ✅ Modular architecture for future builder integrations

**Current Integration:**

- Elementor 3.x+ (full support)

**Requirements:**

- WordPress 6.0+
- PHP 7.4+ (8.x supported)

## Installation

### Via WordPress.org

```
1. Navigate to Plugins → Add New
2. Search for "Spectre Icons"
3. Click Install Now → Activate
4. Configure at Settings → Spectre Icons
```

### Manual Install

```
1. Download plugin ZIP
2. Upload via Plugins → Add New → Upload Plugin
3. Activate and configure
```

### From Source

```bash
# Lint PHP
bin/lint-php.sh

# Install as plugin (symlink or copy to wp-content/plugins/)
```

## Usage

### 1. Enable Icon Libraries

Navigate to **Settings → Spectre Icons** and toggle which icon libraries appear in Elementor's icon picker.

### 2. Use Icons in Elementor

Open any Elementor widget that supports icons:

- Icon widget
- Icon Box widget
- Icon List widget
- Social Icons widget

Select "Lucide Icons" or "Font Awesome" from the picker tabs.

### 3. Customize Colors

**Custom Colors:**
Use Elementor's color picker to set any icon color.

**Official Branding (Social Icons):**
Select "Official Color" mode for automatic white icons on branded backgrounds (Facebook blue, Twitter blue, etc.).

## How It Works

```
1. SVG packs → assets/iconpacks/
2. JSON manifests (assets/manifests/)
3. Plugin registers manifests as Elementor libraries
4. JavaScript injects inline SVGs in editor + frontend
5. CSS handles color inheritance + branding
```

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
│   ├── manifests/                 # JSON manifests
│   ├── js/                        # Client-side SVG injection
│   └── css/                       # Icon styling
└── .wordpress-org/                # Plugin directory assets
```

## Adding Custom Icon Packs

### 1. Add SVG Files

```
assets/iconpacks/your-pack-name/
```

### 2. Register Library

Add definition to `includes/elementor/icon-libraries.php`:

```php
'your-pack-slug' => array(
    'label'            => __( 'Your Pack Name', 'spectre-icons' ),
    'description'      => __( 'Description.', 'spectre-icons' ),
    'manifest'         => 'spectre-your-pack.json',
    'display_prefix'   => 'yourpack',
    'class_prefix'     => 'yourpack-',
    'label_icon'       => 'eicon-star',
    'style'            => 'outline', // or 'filled'
    'preview_selector' => 'i[class*="yourpack-"]',
),
```

### 3. Test

Enable at **Settings → Spectre Icons**, refresh Elementor editor, verify picker + frontend.

## Troubleshooting

| Issue                         | Solution                                                                                                                                                                                  |
| ----------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Icons not rendering           | • Verify manifests exist (`assets/manifests/*.json`)<br>• Check DevTools Network tab (200 OK)<br>• Enable library at **Settings → Spectre Icons**<br>• Hard refresh editor (Ctrl+Shift+R) |
| Custom colors not working     | • Verify color picker has value set<br>• Check CSS loads in DevTools<br>• Ensure NOT using "Official Color" mode                                                                          |
| Official branding not working | • Select "Official Color" (not "Custom")<br>• Use Social Icons widget specifically<br>• Check CSS specificity in DevTools                                                                 |
| PHP errors in logs            | • Debug notices require `WP_DEBUG` enabled<br>• Verify manifests exist and are readable<br>• Check library registration in code                                                           |

## Repository Layout

| Folder                | Responsibility                                                       |
| --------------------- | -------------------------------------------------------------------- |
| `spectre-icons.php`   | Plugin bootstrap and core definitions                                |
| `includes/elementor/` | Elementor-specific integration (library manager, renderer, settings) |
| `assets/iconpacks/`   | Raw SVG files organized by library                                   |
| `assets/manifests/`   | JSON manifests                                                       |
| `assets/js/`          | Client-side SVG injection for editor and frontend                    |
| `assets/css/`         | Icon styling, color inheritance, and branding support                |
| `.wordpress-org/`     | WordPress.org plugin directory assets (banners, icons)               |

## Part of the Spectre Suite

- **Spectre Tokens** – Design token foundation
- **Spectre UI** – Core styling layer
- **Spectre Icons** – Icon library for WordPress (this plugin)
- **Spectre Blocks** – WordPress block library
- **Spectre Astro** – Astro integration
- **Spectre 11ty** – Eleventy integration

## Contributing

Issues and pull requests are welcome. For detailed contribution guidelines, see **[CONTRIBUTING.md](CONTRIBUTING.md)**.

## License

GPL-2.0-or-later © PHCDevworks

**Bundled Icon Libraries:**

- **Lucide** – ISC License ([lucide.dev](https://lucide.dev))
- **Font Awesome Free** – CC BY 4.0 (icons) / MIT (code) ([fontawesome.com/license](https://fontawesome.com/license/free))

See **[LICENSE.md](LICENSE.md)** for details.

---

## ❤️ Support Spectre

If Spectre Icons helps your workflow, consider sponsoring:

- [GitHub Sponsors](https://github.com/sponsors/phcdevworks)
- [Buy Me a Coffee](https://buymeacoffee.com/phcdevworks)

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
