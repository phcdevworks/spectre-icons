# Contributing to Spectre Icons

Thanks for helping improve Spectre Icons! This WordPress plugin provides curated icon libraries for Elementor with manifest-driven performance and seamless color integration. Keeping changes intentional and well-documented ensures a stable experience for all WordPress users.

## Development Setup

1. Clone the repo:

```bash
git clone https://github.com/phcdevworks/spectre-icons.git
cd spectre-icons
```

2. Generate icon manifests:

```bash
php bin/generate-icon-manifests.php
```

3. Install development dependencies (for code quality checks):

```bash
composer install
```

4. Verify PHP syntax and coding standards:

```bash
bin/lint-php.sh
vendor/bin/phpcs --standard=WordPress spectre-icons.php includes/
```

## Project Structure

- `spectre-icons.php` – Plugin bootstrap and core definitions
- `includes/elementor/` – Elementor-specific integration
  - `class-spectre-icons-elementor-library-manager.php` – Library registration
  - `class-spectre-icons-elementor-manifest-renderer.php` – SVG rendering engine
  - `class-spectre-icons-elementor-settings.php` – Admin settings UI
  - `icon-libraries.php` – Library definitions and registration
  - `integration-hooks.php` – WordPress/Elementor hooks
- `assets/iconpacks/` – Raw SVG files organized by library
- `assets/manifests/` – Generated JSON manifests (do not edit by hand)
- `assets/js/` – Client-side SVG injection
- `assets/css/` – Icon styling and color inheritance
- `bin/generate-icon-manifests.php` – Manifest generation script
- `.wordpress-org/` – WordPress.org plugin directory assets

## Guidelines

### Icons & Manifests

1. **Source SVGs**: Place icon files in `assets/iconpacks/<library-name>/`
2. **Generate manifests**: Always run `php bin/generate-icon-manifests.php` after adding/updating icons
3. **Commit both**: Check in both the SVG files and the generated JSON manifests
4. **Icon licensing**: Ensure any new icon packs have compatible licenses (GPL-compatible)

### PHP & WordPress Standards

- Follow WordPress Coding Standards (WPCS)
- Use proper escaping (`esc_html`, `esc_attr`) for all output
- Sanitize all input (`sanitize_text_field`, `sanitize_key`)
- Add `phpcs:ignore` comments only when necessary with clear justification
- Test with PHP 7.4+ and 8.x
- Ensure WordPress 6.0+ compatibility

### Elementor Integration

- Test changes with Elementor 3.x+ (editor and frontend)
- Verify icon picker functionality and live preview
- Ensure color inheritance works across all icon widgets (Icon, Icon Box, Icon List, Social Icons)
- Test both "Official Color" branding and custom color picker modes

### Documentation

- Update `README.MD` when adding features or changing usage
- Update `readme.txt` for WordPress.org changelog
- Add inline comments for complex logic
- Document filter hooks and action hooks for extensibility

## Pull Request Process

1. Create a feature branch from `main`
2. Make your changes with proper testing:
   - Run `bin/lint-php.sh` for syntax checks
   - Regenerate manifests if icons changed
   - Test in a WordPress + Elementor environment
3. Update documentation (`README.MD`, `readme.txt`) as needed
4. Commit generated files (`assets/manifests/*.json`)
5. Open a PR with:
   - Clear description of the change
   - Screenshots/videos if UI-related
   - Testing steps performed

## Testing Checklist

Before submitting a PR, verify:

- [ ] PHP syntax is valid (`bin/lint-php.sh`)
- [ ] Manifests are regenerated and committed
- [ ] Icons render correctly in Elementor editor
- [ ] Icons display properly on frontend
- [ ] Color customization works (custom colors)
- [ ] Official branding works (white icons on branded backgrounds)
- [ ] No JavaScript console errors
- [ ] Settings page functions correctly
- [ ] Compatible with WordPress 6.0+ and Elementor 3.x+

## Questions?

Open an issue on GitHub or start a discussion if you're unsure about the best way to approach a change.

## License

By contributing, you agree that your contributions will be licensed under GPL-2.0-or-later.
